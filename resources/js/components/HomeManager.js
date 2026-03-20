import Alpine from "alpinejs";

Alpine.data('homeManager', (username, isMe) => ({
    username,
    isMe,
    editing: false,
    saving: false,
    activeBackground: null,
    placedItems: [],
    removedItemIds: [],
    selectedItem: null,
    dragging: null,
    dragOffset: { x: 0, y: 0 },
    toast: null,

    showBag: false,
    bagTab: 'inventory',

    invTab: 'stickers',
    inventory: { stickers: [], notes: [], widgets: [], backgrounds: [] },
    invActive: null,
    placeQty: 1,
    invLoading: false,

    shopTab: 'home',
    shopCategories: [],
    shopItems: [],
    shopActive: null,
    buyQty: 1,
    totalPrice: 0,
    shopLoading: false,
    buying: false,

    init() {
        this.fetchPlacedItems();
        this._bindDrag();
    },

    _showToast(msg, type = 'success') {
        this.toast = { msg, type };
        setTimeout(() => this.toast = null, 3000);
    },

    _bindDrag() {
        let dragEl = null;

        const onStart = (e) => {
            if (!this.editing) return;
            if (e.target.closest('[data-no-drag]')) return;
            const el = e.target.closest('[data-home-item]');
            if (!el) return;

            const id = Number(el.dataset.homeItem);
            const item = this.placedItems.find(i => i.id === id);
            if (!item || this.removedItemIds.includes(id)) return;

            const container = el.closest('.home-canvas');
            if (!container) return;

            const rect = container.getBoundingClientRect();
            const cx = e.touches ? e.touches[0].clientX : e.clientX;
            const cy = e.touches ? e.touches[0].clientY : e.clientY;

            this.dragging = item;
            this.selectedItem = item;
            dragEl = el;
            this.dragOffset = { x: cx - rect.left - (item.x || 0), y: cy - rect.top - (item.y || 0) };
            item.z = this._nextZ();
            e.preventDefault();
        };

        const onMove = (e) => {
            if (!this.dragging || !dragEl) return;
            const container = document.querySelector('.home-canvas');
            if (!container) return;

            const rect = container.getBoundingClientRect();
            const elW = dragEl.offsetWidth || 40;
            const elH = dragEl.offsetHeight || 40;
            const cx = e.touches ? e.touches[0].clientX : e.clientX;
            const cy = e.touches ? e.touches[0].clientY : e.clientY;

            let x = cx - rect.left - this.dragOffset.x;
            let y = cy - rect.top - this.dragOffset.y;

            x = Math.max(0, Math.min(x, rect.width - elW));
            y = Math.max(0, Math.min(y, rect.height - elH));

            this.dragging.x = Math.round(x);
            this.dragging.y = Math.round(y);
            e.preventDefault();
        };

        const onEnd = () => { this.dragging = null; dragEl = null; };

        document.addEventListener('mousedown', onStart);
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onEnd);
        document.addEventListener('touchstart', onStart, { passive: false });
        document.addEventListener('touchmove', onMove, { passive: false });
        document.addEventListener('touchend', onEnd);
    },

    _nextZ() {
        if (!this.placedItems.length) return 1;
        return Math.max(...this.placedItems.map(i => i.z || 0)) + 1;
    },

    _randomPos() {
        return {
            x: 60 + Math.floor(Math.random() * 700),
            y: 60 + Math.floor(Math.random() * 400),
        };
    },

    async fetchPlacedItems() {
        try {
            const res = await this._api(`/home/${this.username}/placed-items`);
            this.activeBackground = res.activeBackground || null;
            this.placedItems = res.items || [];
            for (const item of this.placedItems) {
                if (item.home_item?.type === 'w') this.fetchWidgetContent(item);
            }
        } catch (e) { console.error(e); }
    },

    async fetchWidgetContent(item) {
        try {
            const res = await this._api(`/home/${this.username}/widget-content/${item.id}`);
            item.content = res.content;
            item.widget_type = res.widget_type;
        } catch (e) { console.error(e); }
    },

    bg() { return this.activeBackground?.home_item?.image || ''; },
    visible() { return this.placedItems.filter(i => !this.removedItemIds.includes(i.id)); },

    select(item) {
        if (!this.editing) return;
        this.selectedItem = this.selectedItem?.id === item.id ? null : item;
    },

    remove(item) {
        if (!this.editing) return;
        this.removedItemIds.push(item.id);
        this._returnToInv(item);
        if (this.selectedItem?.id === item.id) this.selectedItem = null;
    },

    _returnToInv(item) {
        const tab = this._typeTab(item.home_item?.type);
        const existing = this.inventory[tab]?.find(i => i.home_item_id === (item.home_item_id || item.home_item?.id));
        if (existing) {
            if (!existing.item_ids) existing.item_ids = [];
            existing.item_ids.push(item.id);
        } else if (this.inventory[tab]) {
            this.inventory[tab].push({
                home_item_id: item.home_item_id || item.home_item?.id,
                home_item: item.home_item,
                item_ids: [item.id],
            });
        }
    },

    _typeTab(type) {
        return { s: 'stickers', w: 'widgets', b: 'backgrounds', n: 'notes' }[type] || 'notes';
    },

    async save() {
        this.saving = true;
        try {
            const items = [
                ...this.visible().map(i => ({
                    id: i.id, x: i.x || 0, y: i.y || 0, z: i.z || 0,
                    is_reversed: i.is_reversed || false, theme: i.theme || null,
                    placed: true, extra_data: i.extra_data || '',
                })),
                ...this.removedItemIds.map(id => {
                    const i = this.placedItems.find(p => p.id === id);
                    return i ? { id: i.id, x: 0, y: 0, z: 0, is_reversed: false, theme: null, placed: false, extra_data: '' } : null;
                }).filter(Boolean),
            ];

            const res = await this._api(`/home/${this.username}/save`, 'POST', {
                items, backgroundId: this.activeBackground?.id || 0,
            });

            if (res.success && res.href) window.location.href = res.href;
        } catch (e) { console.error(e); }
        finally { this.saving = false; }
    },

    cancel() {
        this.editing = false;
        this.selectedItem = null;
        this.removedItemIds = [];
        this.showBag = false;
        this.fetchPlacedItems();
    },

    openBag(tab) {
        this.bagTab = tab;
        this.showBag = true;
        this.shopActive = null;
        this.invActive = null;
        if (tab === 'inventory') this.fetchInv();
        else this.fetchShopCats();
    },

    async fetchInv() {
        this.invLoading = true;
        try {
            const res = await this._api(`/home/${this.username}/inventory`);
            this.inventory = res.inventory || { stickers: [], notes: [], widgets: [], backgrounds: [] };
        } catch (e) { console.error(e); }
        finally { this.invLoading = false; }
    },

    invItems() {
        return (this.inventory[this.invTab] || []).filter(i => i.item_ids?.length > 0);
    },

    // Double-click to quick-place from inventory
    quickPlace(item) {
        this.invActive = item;
        this.placeQty = 1;
        this.place();
    },

    place() {
        if (!this.invActive) return;
        const item = this.invActive;
        const type = item.home_item?.type;

        if (type === 'b') {
            if (!item.item_ids.length) return;
            const id = item.item_ids.shift();
            if (this.activeBackground) this._returnToInv(this.activeBackground);
            this.activeBackground = { id, home_item_id: item.home_item_id, home_item: item.home_item };
            this._showToast('Background applied');
        } else if (type === 'w') {
            if (!item.item_ids.length) return;
            const id = item.item_ids.shift();
            const pos = this._randomPos();
            const n = { id, home_item_id: item.home_item_id, home_item: item.home_item, ...pos, z: this._nextZ(), is_reversed: false, theme: 'default', content: null, widget_type: null };
            this.placedItems.push(n);
            this.fetchWidgetContent(n);
            this._showToast('Widget placed');
        } else {
            const qty = Math.min(this.placeQty, item.item_ids.length);
            for (let i = 0; i < qty; i++) {
                const id = item.item_ids.shift();
                const pos = this._randomPos();
                if (this.removedItemIds.includes(id)) {
                    this.removedItemIds = this.removedItemIds.filter(r => r !== id);
                    const ex = this.placedItems.find(p => p.id === id);
                    if (ex) { ex.x = pos.x; ex.y = pos.y; ex.z = this._nextZ(); }
                } else {
                    this.placedItems.push({
                        id, home_item_id: item.home_item_id, home_item: item.home_item,
                        ...pos, z: this._nextZ(), is_reversed: false,
                        theme: type === 'n' ? 'note' : null, extra_data: '', parsed_data: '',
                    });
                }
            }
            this._showToast(`${qty} item${qty > 1 ? 's' : ''} placed`);
        }
        this.invActive = null;
        this.placeQty = 1;
        this.showBag = false;
    },

    async fetchShopCats() {
        if (this.shopCategories.length) return;
        this.shopLoading = true;
        try {
            const res = await this._api('/home/shop/categories');
            this.shopCategories = res.categories || [];
        } catch (e) { console.error(e); }
        finally { this.shopLoading = false; }
    },

    setShopTab(tab) {
        this.shopTab = tab;
        this.shopActive = null;
        this.buyQty = 1;
        this.totalPrice = 0;
        this.shopItems = [];
        if (tab !== 'home' && tab !== 'categories') this._fetchShopType(tab);
    },

    async openCat(id) {
        this.shopTab = 'cat-' + id;
        this.shopActive = null;
        this.shopLoading = true;
        try {
            const res = await this._api(`/home/shop/category/${id}/items`);
            this.shopItems = res.items || [];
        } catch (e) { console.error(e); }
        finally { this.shopLoading = false; }
    },

    async _fetchShopType(type) {
        this.shopLoading = true;
        try {
            const res = await this._api(`/home/shop/type/${type}/items`);
            this.shopItems = res.items || [];
        } catch (e) { console.error(e); }
        finally { this.shopLoading = false; }
    },

    pickShop(item) {
        this.shopActive = item;
        this.buyQty = 1;
        this.totalPrice = item.price;
    },

    calcPrice() {
        if (!this.shopActive) return;
        this.buyQty = Math.max(1, Math.min(100, parseInt(this.buyQty) || 1));
        this.totalPrice = this.shopActive.price * this.buyQty;
    },

    async buy() {
        if (this.buying || !this.shopActive) return;
        this.buying = true;
        try {
            const res = await this._api(`/home/${this.username}/buy-item`, 'POST', {
                item_id: this.shopActive.id, quantity: this.buyQty,
            });

            if (res.success && res.items) {
                for (const p of res.items) {
                    const tab = this._typeTab(p.home_item?.type);
                    const ex = this.inventory[tab]?.find(i => i.home_item_id === p.home_item_id);
                    if (ex) ex.item_ids = [...(ex.item_ids || []), ...p.item_ids];
                    else if (this.inventory[tab]) this.inventory[tab].push({ home_item_id: p.home_item_id, home_item: p.home_item, item_ids: p.item_ids });
                }
                this._showToast(res.message);
                this.shopActive = null;
                this.buyQty = 1;
                this.totalPrice = 0;
            } else {
                this._showToast(res.message || 'Purchase failed', 'error');
            }
        } catch (e) { console.error(e); }
        finally { this.buying = false; }
    },

    // After buying, switch to inventory to place the item immediately
    async buyAndPlace() {
        if (this.buying || !this.shopActive) return;
        this.buying = true;
        const shopItem = this.shopActive;
        try {
            const res = await this._api(`/home/${this.username}/buy-item`, 'POST', {
                item_id: shopItem.id, quantity: this.buyQty,
            });

            if (res.success && res.items) {
                for (const p of res.items) {
                    const type = p.home_item?.type;
                    for (const id of p.item_ids) {
                        const pos = this._randomPos();
                        if (type === 'b') {
                            if (this.activeBackground) this._returnToInv(this.activeBackground);
                            this.activeBackground = { id, home_item_id: p.home_item_id, home_item: p.home_item };
                        } else if (type === 'w') {
                            const n = { id, home_item_id: p.home_item_id, home_item: p.home_item, ...pos, z: this._nextZ(), is_reversed: false, theme: 'default', content: null, widget_type: null };
                            this.placedItems.push(n);
                            this.fetchWidgetContent(n);
                        } else {
                            this.placedItems.push({
                                id, home_item_id: p.home_item_id, home_item: p.home_item,
                                ...pos, z: this._nextZ(), is_reversed: false,
                                theme: type === 'n' ? 'note' : null, extra_data: '', parsed_data: '',
                            });
                        }
                    }
                }
                this._showToast(res.message);
                this.shopActive = null;
                this.buyQty = 1;
                this.totalPrice = 0;
                this.showBag = false;
            } else {
                this._showToast(res.message || 'Purchase failed', 'error');
            }
        } catch (e) { console.error(e); }
        finally { this.buying = false; }
    },

    currIcon(type) {
        return { '-1': '/assets/images/icons/currency/credits.png', '0': '/assets/images/icons/currency/duckets.png', '5': '/assets/images/icons/currency/diamonds.png', '101': '/assets/images/icons/currency/credits.png' }[String(type)] || '/assets/images/icons/currency/credits.png';
    },

    async _api(url, method = 'GET', body = null) {
        const opts = { method, headers: { Accept: 'application/json' } };
        if (body) {
            opts.headers['Content-Type'] = 'application/json';
            opts.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content;
            opts.body = JSON.stringify(body);
        }
        const res = await fetch(url, opts);
        return res.json();
    },
}));
