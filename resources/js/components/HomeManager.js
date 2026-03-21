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
    invSelected: [],
    placeQty: 1,
    invLoading: false,

    shopTab: 'home',
    shopCategories: [],
    shopItems: [],
    shopActive: null,
    shopSelected: [],
    buyQty: 1,
    totalPrice: 0,
    shopLoading: false,
    buying: false,
    previewing: false,
    previewItems: [],
    previewBg: null,
    _previewTimer: null,

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
            if (!item || this.removedItemIds.includes(id) || item._preview) return;

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

    img(path) {
        if (!path) return '';
        if (path.startsWith('/') || path.startsWith('http')) return path;
        return `/storage/${path}`;
    },
    bg() {
        if (this.previewBg) return this.img(this.previewBg.image);
        return this.img(this.activeBackground?.home_item?.image);
    },
    visible() {
        const placed = this.placedItems.filter(i => !this.removedItemIds.includes(i.id));
        return [...placed, ...this.previewItems];
    },

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
        this.invSelected = [];
        this.shopSelected = [];
        if (tab === 'inventory') this.fetchInv();
        else this.fetchShopCats();
    },

    // ─── Inventory ───

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

    invIsSelected(item) {
        return this.invSelected.some(s => s.home_item_id === item.home_item_id);
    },

    invToggle(item) {
        if (this.invIsSelected(item)) {
            this.invSelected = this.invSelected.filter(s => s.home_item_id !== item.home_item_id);
        } else {
            this.invSelected.push(item);
        }
        this.invActive = this.invSelected.length === 1 ? this.invSelected[0] : null;
    },

    invSelectAll() {
        const items = this.invItems();
        if (this.invSelected.length === items.length) {
            this.invSelected = [];
            this.invActive = null;
        } else {
            this.invSelected = [...items];
            this.invActive = null;
        }
    },

    quickPlace(item) {
        this.invSelected = [item];
        this.invActive = item;
        this.placeQty = 1;
        this.place();
    },

    _placeOne(item, qty = 1) {
        const type = item.home_item?.type;
        if (type === 'b') {
            if (!item.item_ids.length) return 0;
            const id = item.item_ids.shift();
            if (this.activeBackground) this._returnToInv(this.activeBackground);
            this.activeBackground = { id, home_item_id: item.home_item_id, home_item: item.home_item };
            return 1;
        } else if (type === 'w') {
            if (!item.item_ids.length) return 0;
            const id = item.item_ids.shift();
            const pos = this._randomPos();
            const n = { id, home_item_id: item.home_item_id, home_item: item.home_item, ...pos, z: this._nextZ(), is_reversed: false, theme: 'default', content: null, widget_type: null };
            this.placedItems.push(n);
            this.fetchWidgetContent(n);
            return 1;
        } else {
            const actual = Math.min(qty, item.item_ids.length);
            for (let i = 0; i < actual; i++) {
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
            return actual;
        }
    },

    place() {
        let totalPlaced = 0;
        const targets = this.invSelected.length > 0 ? this.invSelected : (this.invActive ? [this.invActive] : []);
        if (!targets.length) return;

        for (const item of targets) {
            const qty = (targets.length === 1) ? this.placeQty : 1;
            totalPlaced += this._placeOne(item, qty);
        }

        this._showToast(`${totalPlaced} item${totalPlaced !== 1 ? 's' : ''} placed`);
        this.invActive = null;
        this.invSelected = [];
        this.placeQty = 1;
        this.showBag = false;
    },

    // ─── Shop ───

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
        this.shopSelected = [];
        this.buyQty = 1;
        this.totalPrice = 0;
        this.shopItems = [];
        if (tab !== 'home' && tab !== 'categories') this._fetchShopType(tab);
    },

    async openCat(id) {
        this.shopTab = 'cat-' + id;
        this.shopActive = null;
        this.shopSelected = [];
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

    shopIsSelected(item) {
        return this.shopSelected.some(s => s.id === item.id);
    },

    shopToggle(item) {
        if (this.shopIsSelected(item)) {
            this.shopSelected = this.shopSelected.filter(s => s.id !== item.id);
        } else {
            this.shopSelected.push(item);
        }
        this.shopActive = this.shopSelected.length === 1 ? this.shopSelected[0] : null;
        this._calcBatchPrice();
    },

    shopSelectAll() {
        if (this.shopSelected.length === this.shopItems.length) {
            this.shopSelected = [];
            this.shopActive = null;
        } else {
            this.shopSelected = [...this.shopItems];
            this.shopActive = null;
        }
        this._calcBatchPrice();
    },

    pickShop(item) {
        this.shopActive = item;
        this.shopSelected = [item];
        this.buyQty = 1;
        this.totalPrice = item.price;
    },

    _calcBatchPrice() {
        if (this.shopSelected.length <= 1) {
            this.totalPrice = this.shopActive?.price || 0;
        } else {
            this.totalPrice = this.shopSelected.reduce((sum, i) => sum + i.price, 0);
        }
    },

    calcPrice() {
        if (!this.shopActive) return;
        this.buyQty = Math.max(1, Math.min(100, parseInt(this.buyQty) || 1));
        this.totalPrice = this.shopActive.price * this.buyQty;
    },

    async buy() {
        const targets = this.shopSelected.length > 0 ? this.shopSelected : (this.shopActive ? [this.shopActive] : []);
        if (this.buying || !targets.length) return;
        this.buying = true;

        let ok = 0, fail = 0;
        for (const item of targets) {
            try {
                const qty = targets.length === 1 ? this.buyQty : 1;
                const res = await this._api(`/home/${this.username}/buy-item`, 'POST', {
                    item_id: item.id, quantity: qty,
                });
                if (res.success && res.items) {
                    for (const p of res.items) {
                        const tab = this._typeTab(p.home_item?.type);
                        const ex = this.inventory[tab]?.find(i => i.home_item_id === p.home_item_id);
                        if (ex) ex.item_ids = [...(ex.item_ids || []), ...p.item_ids];
                        else if (this.inventory[tab]) this.inventory[tab].push({ home_item_id: p.home_item_id, home_item: p.home_item, item_ids: p.item_ids });
                    }
                    ok++;
                } else { fail++; }
            } catch { fail++; }
        }

        if (ok) this._showToast(`${ok} item${ok !== 1 ? 's' : ''} purchased`);
        if (fail) this._showToast(`${fail} purchase${fail !== 1 ? 's' : ''} failed`, 'error');
        this.shopActive = null;
        this.shopSelected = [];
        this.buyQty = 1;
        this.totalPrice = 0;
        this.buying = false;
    },

    async buyAndPlace() {
        const targets = this.shopSelected.length > 0 ? this.shopSelected : (this.shopActive ? [this.shopActive] : []);
        if (this.buying || !targets.length) return;
        this.buying = true;

        let totalPlaced = 0;
        for (const shopItem of targets) {
            try {
                const qty = targets.length === 1 ? this.buyQty : 1;
                const res = await this._api(`/home/${this.username}/buy-item`, 'POST', {
                    item_id: shopItem.id, quantity: qty,
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
                            totalPlaced++;
                        }
                    }
                }
            } catch (e) { console.error(e); }
        }

        if (totalPlaced) this._showToast(`${totalPlaced} item${totalPlaced !== 1 ? 's' : ''} purchased & placed`);
        this.shopActive = null;
        this.shopSelected = [];
        this.buyQty = 1;
        this.totalPrice = 0;
        this.showBag = false;
        this.buying = false;
    },

    // ─── Preview ───

    preview(item) {
        this.endPreview();
        const type = item?.type;

        if (type === 'b') {
            this.previewBg = item;
        } else {
            this.previewItems = [{
                id: 'preview-' + item.id,
                home_item_id: item.id,
                home_item: item,
                ...this._randomPos(),
                z: this._nextZ(),
                is_reversed: false,
                theme: type === 'w' ? 'default' : (type === 'n' ? 'note' : null),
                extra_data: '',
                parsed_data: '',
                content: type === 'w' ? '<p class="text-xs text-gray-400 italic">Preview</p>' : null,
                _preview: true,
            }];
        }

        this.previewing = true;
        this.showBag = false;
        this._showToast('Previewing — click End Preview when done');

        this._previewTimer = setTimeout(() => this.endPreview(), 15000);
    },

    endPreview() {
        if (this._previewTimer) { clearTimeout(this._previewTimer); this._previewTimer = null; }
        this.previewItems = [];
        this.previewBg = null;
        this.previewing = false;
    },

    previewSelected() {
        const targets = this.shopSelected.length > 0 ? this.shopSelected : (this.shopActive ? [this.shopActive] : []);
        if (!targets.length) return;

        this.endPreview();
        const items = [];
        let bgSet = false;

        for (const item of targets) {
            if (item.type === 'b' && !bgSet) {
                this.previewBg = item;
                bgSet = true;
            } else {
                items.push({
                    id: 'preview-' + item.id,
                    home_item_id: item.id,
                    home_item: item,
                    ...this._randomPos(),
                    z: this._nextZ() + items.length,
                    is_reversed: false,
                    theme: item.type === 'w' ? 'default' : (item.type === 'n' ? 'note' : null),
                    extra_data: '',
                    parsed_data: '',
                    content: item.type === 'w' ? '<p class="text-xs text-gray-400 italic">Preview</p>' : null,
                    _preview: true,
                });
            }
        }

        this.previewItems = items;
        this.previewing = true;
        this.showBag = false;
        this._showToast(`Previewing ${targets.length} item${targets.length !== 1 ? 's' : ''}`);

        this._previewTimer = setTimeout(() => this.endPreview(), 15000);
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
