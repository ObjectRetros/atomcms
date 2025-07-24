<x-app-layout>
    @push('title', __('Badge Generator'))

    <script>
    const translations = {
        buy_confirmation: @json(__('badge_purchase_confirmation', ['cost' => $cost, 'currency' => $currencyType])),
        purchase_success: @json(__('badge_purchase_success', ['currency' => ucfirst($currencyType)])),
        purchase_error_insufficient: @json(__('badge_purchase_error_insufficient', ['currency' => $currencyType])),
        purchase_error_general: @json(__('badge_purchase_error_general')),
        missing_fields: @json(__('Please fill in the badge name, description, and draw something on the canvas.')),
        invalid_content: @json(__('Badge name and description cannot contain URLs.')),
        invalid_file_type: @json(__('Only PNG and GIF files are allowed.'))
    };
    </script>

    <div class="col-span-12">
        <x-content.content-card icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Badge Drawer') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Draw your very own badge') }}
            </x-slot:under-title>

            <div class="px-2 text-sm dark:text-gray-200">
                @if ($folderError)
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error:</strong>
                        <span class="block sm:inline">{{ $errorMessage }}</span>
                    </div>
                @endif
                <div x-data="badgeDrawer({ cost: {{ $cost }}, currencyType: '{{ $currencyType }}' })" class="mt-4">
                    <div class="flex flex-col md:flex-row gap-4 md:gap-8 items-start">
                        <div class="checkerboard w-full max-w-[640px] aspect-square relative">
                            <div id="guide" x-ref="guide" class="border-t border-l border-gray-700 dark:border-white"></div>
                            <canvas width="40" height="40" x-ref="canvas" class="w-full h-full border border-gray-300 dark:border-gray-700" style="image-rendering: pixelated; background: transparent;"></canvas>
                        </div>
                        <div class="flex flex-col w-full md:w-auto">
                            <h3 class="font-bold mb-2">{{ __('Preview') }}</h3>
                            <div class="checkerboard w-10 h-10 md:w-[40px] md:h-[40px]">
                                <canvas width="40" height="40" x-ref="previewCanvas" class="border border-gray-300 dark:border-gray-700" style="image-rendering: pixelated; background: transparent;"></canvas>
                            </div>
                            <div class="flex flex-wrap gap-4 mt-4 items-center">
                                <div>
                                    <label>{{ __('Choose Color') }}</label>
                                    <div @click="$refs.colorInput.click()" class="w-16 h-16 cursor-pointer flex items-center justify-center relative">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[1.8rem] h-[1.8rem]" fill="none" stroke="black" stroke-width="1" viewBox="0 0 20 20">
                                            <circle cx="10" cy="10" r="10" fill="white" stroke="none"/>
                                            <g transform="translate(2,2)">
                                                <path d="M6.192 2.78c-.458-.677-.927-1.248-1.35-1.643a3 3 0 0 0-.71-.515c-.217-.104-.56-.205-.882-.02-.367.213-.427.63-.43.896-.003.304.064.664.173 1.044.196.687.556 1.528 1.035 2.402L.752 8.22c-.277.277-.269.656-.218.918.055.283.187.593.36.903.348.627.92 1.361 1.626 2.068.707.707 1.441 1.278 2.068 1.626.31.173.62.305.903.36.262.05.64.059.918-.218l5.615-5.615c.118.257.092.512.05.939-.03.292-.068.665-.073 1.176v.123h.003a1 1 0 0 0 1.993 0H14v-.057a1 1 0 0 0-.004-.117c-.055-1.25-.7-2.738-1.86-3.494a4 4 0 0 0-.211-.434c-.349-.626-.92-1.36-1.627-2.067S8.857 3.052 8.23 2.704c-.31-.172-.62-.304-.903-.36-.262-.05-.64-.058-.918.219zM4.16 1.867c.381.356.844.922 1.311 1.632l-.704.705c-.382-.727-.66-1.402-.813-1.938a3.3 3.3 0 0 1-.131-.673q.137.09.337.274m.394 3.965c.54.852 1.107 1.567 1.607 2.033a.5.5 0 1 0 .682-.732c-.453-.422-1.017-1.136-1.564-2.027l1.088-1.088q.081.181.183.365c.349.627.92 1.361 1.627 2.068.706.707 1.44 1.278 2.068 1.626q.183.103.365.183l-4.861 4.862-.068-.01c-.137-.027-.342-.104-.608-.252-.524-.292-1.186-.8-1.846-1.46s-1.168-1.32-1.46-1.846c-.147-.265-.225-.47-.251-.607l-.01-.068zm2.87-1.935a2.4 2.4 0 0 1-.241-.561c.135.033.324.11.562.241.524.292 1.186.8 1.846 1.46.45.45.83.901 1.118 1.31a3.5 3.5 0 0 0-1.066.091 11 11 0 0 1-.76-.694c-.66-.66-1.167-1.322-1.458-1.847z"/>
                                            </g>
                                        </svg>
                                        <input type="color" id="colorInput" x-ref="colorInput" x-model="color" class="absolute opacity-0 w-full h-full cursor-pointer top-0 left-0" @input="color = $event.target.value" >
                                    </div>
                                </div>
                                <div>
                                    <div :style="'background-color: ' + color" class="w-16 h-16 border border-gray-300 dark:border-gray-700"></div>
                                </div>
                                <div>
                                    <h4 class="font-bold mb-1">{{ __('Palette') }}</h4>
                                    <div class="grid grid-cols-6 gap-1">
                                        <template x-for="col in colors">
                                            <div @click="color = col; eraseMode = false" :style="'background-color: ' + col" class="w-6 h-6 cursor-pointer border border-gray-300 dark:border-gray-700"></div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <h4 class="font-bold mb-1">{{ __('Recent color') }}</h4>
                                <div class="flex flex-row gap-[2px] overflow-x-auto">
                                    <template x-for="col in recentColors" :key="col">
                                        <div @click="color = col; eraseMode = false" :style="'background-color: ' + col" class="w-6 h-6 cursor-pointer border border-gray-300 dark:border-gray-700 flex-shrink-0"></div>
                                    </template>
                                    <template x-for="i in (12 - recentColors.length)" :key="'empty-' + i">
                                        <div class="w-6 h-6 border border-gray-300 dark:border-gray-700 bg-transparent flex-shrink-0"></div>
                                    </template>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button @click="toggleCopyMode" class="flex items-center cursor-pointer">
                                    {{ __('Copy color:') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-1" fill="none" :stroke="copyMode ? 'red' : 'black'" stroke-width="1" viewBox="0 0 20 20">
                                        <circle cx="10" cy="10" r="10" fill="white" stroke="none"/>
                                        <g transform="translate(2,2)">
                                            <path d="M13.354.646a1.207 1.207 0 0 0-1.708 0L8.5 3.793l-.646-.647a.5.5 0 1 0-.708.708L8.293 5l-7.147 7.146A.5.5 0 0 0 1 12.5v1.793l-.854.853a.5.5 0 1 0 .708.707L1.707 15H3.5a.5.5 0 0 0 .354-.146L11 7.707l1.146 1.147a.5.5 0 0 0 .708-.708l-.647-.646 3.147-3.146a1.207 1.207 0 0 0 0-1.708zM2 12.707l7-7L10.293 7l-7 7H2z"/>
                                        </g>
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-2">
                                <button @click="toggleEraseMode" class="flex items-center cursor-pointer">
                                    {{ __('Erase mode:') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-1" :fill="eraseMode ? 'red' : 'black'" viewBox="0 0 20 20" stroke-width="0">
                                        <circle cx="10" cy="10" r="10" fill="white" stroke="none"/>
                                        <g transform="translate(2,2)">
                                            <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828l3.879-3.879a2 2 0 0 1 2.828 0zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414L10.207 2.914zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293z"/>
                                        </g>
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-2">
                                <button @click="$refs.fileInput.click()" class="flex items-center cursor-pointer">
                                    {{ __('Import Picture:') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-1" fill="none" stroke="black" stroke-width="1.5" viewBox="0 0 30 30">
                                        <circle cx="15" cy="15" r="15" fill="white" stroke="none"/>
                                        <g transform="translate(3,3)">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                                        </g>
                                    </svg>
                                </button>
                                <input type="file" accept="image/png,image/gif" x-ref="fileInput" style="display:none;" @change="importImage($event)">
                            </div>
                            <div class="mt-2">
                                <label for="toggleGuide">{{ __('Show Grid:') }}</label>
                                <input type="checkbox" id="toggleGuide" x-model="showGrid" checked>
                            </div>
                            <div class="mt-2">
                                <label for="badgeName">{{ __('Badge Name:') }}</label>
                                <input type="text" id="badgeName" x-model="badgeName" maxlength="24" class="w-full border border-gray-300 dark:border-gray-700 rounded p-1 text-black">
                            </div>
                            <div class="mt-2">
                                <label for="badgeDescription">{{ __('Badge Description:') }}</label>
                                <input type="text" id="badgeDescription" x-model="badgeDescription" maxlength="255" class="w-full border border-gray-300 dark:border-gray-700 rounded p-1 text-black">
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-10 flex flex-col md:flex-row gap-4 justify-between">
                        <button type="button" @click="clearBoard" class="w-full rounded bg-red-600 hover:bg-red-700 text-white p-2 border-2 border-red-500 transition ease-in-out duration-150 font-semibold">{{ __('Clear All') }}</button>
                        <button type="button" @click="generateCanvas('download')" class="w-full rounded bg-[#eeb425] text-white p-2 border-2 border-yellow-400 transition ease-in-out duration-200 hover:bg-[#d49f1c] font-semibold"> {{ __('Download badge') }} </button>
                        <button type="button" x-text="buttonText" @click="buyBadge" class="w-full rounded text-white p-2 border-2 border-green-500 transition ease-in-out duration-150 font-semibold" :class="{ 'bg-green-600 hover:bg-green-700': isValid, 'bg-gray-600': !isValid }" :disabled="!isValid || {{ $folderError ? 'true' : 'false' }}"></button>
                    </div>
                </div>
            </div>
        </x-content.content-card>
    </div>

    <script src="{{ asset('js/gif/gif.js') }}"></script>

    <script>
        function badgeDrawer({ cost, currencyType }) {
            return {
                cost: cost,
                currencyType: currencyType,
                color: '#000000',
                recentColors: [],
                eraseMode: false,
                copyMode: false,
                showGrid: true,
                isDrawing: false,
                cellSideCount: 40,
                colorHistory: {},
                badgeName: '',
                badgeDescription: '',
                lastBuyTime: null,
                canBuy: true,
                buttonText: `{{ __('Buy Badge') }} (${cost} ${currencyType.charAt(0).toUpperCase() + currencyType.slice(1)})`,
                drawingContext: null,
                previewContext: null,
                guide: null,
                canvas: null,
                previewCanvas: null,
                colors: [
                    '#000000', '#FFFFFF', '#808080', '#C0C0C0', '#FF0000', '#800000',
                    '#FFFF00', '#808000', '#00FF00', '#008000', '#00FFFF', '#008080',
                    '#0000FF', '#000080', '#FF00FF', '#800080', '#FF4500', '#FFA500',
                    '#FFD700', '#F0E68C', '#90EE90', '#98FB98', '#AFEEEE', '#ADD8E6',
                    '#87CEFA', '#6495ED', '#DDA0DD', '#EE82EE', '#A52A2A', '#D2691E',
                    '#CD853F', '#F4A460', '#FFC0CB', '#9370DB', '#228B22', '#20B2AA'
                ],

                get isValid() {
                    return this.badgeName.trim().length > 0 && this.badgeName.trim().length <= 24 && this.badgeDescription.trim().length > 0 && this.badgeDescription.trim().length <= 255 && Object.keys(this.colorHistory).length > 0 && !this.badgeName.match(/https?:\/\/|www\./i) && !this.badgeDescription.match(/https?:\/\/|www\./i) && this.canBuy;
                },

                init() {
                    this.canvas = this.$refs.canvas;
                    this.previewCanvas = this.$refs.previewCanvas;
                    this.guide = this.$refs.guide;
                    this.drawingContext = this.canvas.getContext('2d');
                    this.previewContext = this.previewCanvas.getContext('2d');

                    // Get actual rendered size of the canvas
                    const canvasWidth = this.canvas.clientWidth;
                    const cellSize = canvasWidth / this.cellSideCount;

                    // Setup the guide grid
                    this.guide.style.width = `${canvasWidth}px`;
                    this.guide.style.height = `${canvasWidth}px`; // Square canvas
                    this.guide.style.gridTemplateColumns = `repeat(${this.cellSideCount}, ${cellSize}px)`;
                    this.guide.style.gridTemplateRows = `repeat(${this.cellSideCount}, ${cellSize}px)`;

                    // Clear any existing children
                    this.guide.innerHTML = '';

                    // Create grid cells
                    for (let i = 0; i < this.cellSideCount * this.cellSideCount; i++) {
                        const cell = document.createElement('div');
                        cell.className = 'border-b border-r border-gray-700 dark:border-white';
                        this.guide.appendChild(cell);
                    }

                    // Watch for grid toggle
                    this.$watch('showGrid', (value) => {
                        this.guide.style.display = value ? 'grid' : 'none';
                    });

                    // Watch for erase/copy toggle
                    this.$watch('eraseMode', (value) => {
                        if (value) this.copyMode = false;
                    });
                    this.$watch('copyMode', (value) => {
                        if (value) this.eraseMode = false;
                    });

                    // Update recent colors
                    this.$watch('color', (newColor) => {
                        this.recentColors = this.recentColors.filter(c => c !== newColor);
                        this.recentColors.unshift(newColor);
                        if (this.recentColors.length > 12) {
                            this.recentColors = this.recentColors.slice(0, 12);
                        }
                    });

                    // Mouse events
                    this.canvas.addEventListener('mousedown', this.handleMousedown.bind(this));
                    this.canvas.addEventListener('mousemove', this.handleMousemove.bind(this));
                    this.canvas.addEventListener('mouseup', () => { this.isDrawing = false; });
                    this.canvas.addEventListener('mouseleave', () => { this.isDrawing = false; });

                    // Touch events
                    this.canvas.addEventListener('touchstart', this.handleTouchstart.bind(this));
                    this.canvas.addEventListener('touchmove', this.handleTouchmove.bind(this));
                    this.canvas.addEventListener('touchend', () => { this.isDrawing = false; });
                    this.canvas.addEventListener('touchcancel', () => { this.isDrawing = false; });

                    // Initial preview
                    this.updatePreview();
                },

                toggleCopyMode() {
                    this.copyMode = !this.copyMode;
                    if (this.copyMode) {
                        this.eraseMode = false;
                    }
                },

                toggleEraseMode() {
                    this.eraseMode = !this.eraseMode;
                    if (this.eraseMode) {
                        this.copyMode = false;
                    }
                },

                handleMousedown(e) {
                    if (e.button !== 0) return;

                    const canvasBoundingRect = this.canvas.getBoundingClientRect();
                    const scaleX = this.canvas.width / canvasBoundingRect.width;
                    const scaleY = this.canvas.height / canvasBoundingRect.height;
                    const x = (e.clientX - canvasBoundingRect.left) * scaleX;
                    const y = (e.clientY - canvasBoundingRect.top) * scaleY;
                    const cellX = Math.floor(x);
                    const cellY = Math.floor(y);
                    const currentColor = this.colorHistory[`${cellX}_${cellY}`];

                    if (this.copyMode) {
                        if (currentColor) {
                            this.color = currentColor;
                            this.copyMode = false;
                        }
                    } else if (e.ctrlKey) {
                        if (currentColor) {
                            this.color = currentColor;
                        }
                    } else {
                        this.paintCell(cellX, cellY);
                        this.isDrawing = true;
                    }
                },

                handleMousemove(e) {
                    if (!this.isDrawing) return;

                    const canvasBoundingRect = this.canvas.getBoundingClientRect();
                    const scaleX = this.canvas.width / canvasBoundingRect.width;
                    const scaleY = this.canvas.height / canvasBoundingRect.height;
                    const x = (e.clientX - canvasBoundingRect.left) * scaleX;
                    const y = (e.clientY - canvasBoundingRect.top) * scaleY;
                    const cellX = Math.floor(x);
                    const cellY = Math.floor(y);

                    this.paintCell(cellX, cellY);
                },

                handleTouchstart(e) {
                    e.preventDefault();
                    const touch = e.touches[0];
                    const canvasBoundingRect = this.canvas.getBoundingClientRect();
                    const scaleX = this.canvas.width / canvasBoundingRect.width;
                    const scaleY = this.canvas.height / canvasBoundingRect.height;
                    const x = (touch.clientX - canvasBoundingRect.left) * scaleX;
                    const y = (touch.clientY - canvasBoundingRect.top) * scaleY;
                    const cellX = Math.floor(x);
                    const cellY = Math.floor(y);
                    const currentColor = this.colorHistory[`${cellX}_${cellY}`];

                    if (this.copyMode) {
                        if (currentColor) {
                            this.color = currentColor;
                            this.copyMode = false;
                        }
                    } else {
                        this.paintCell(cellX, cellY);
                        this.isDrawing = true;
                    }
                },

                handleTouchmove(e) {
                    e.preventDefault();
                    if (!this.isDrawing) return;
                    const touch = e.touches[0];
                    const canvasBoundingRect = this.canvas.getBoundingClientRect();
                    const scaleX = this.canvas.width / canvasBoundingRect.width;
                    const scaleY = this.canvas.height / canvasBoundingRect.height;
                    const x = (touch.clientX - canvasBoundingRect.left) * scaleX;
                    const y = (touch.clientY - canvasBoundingRect.top) * scaleY;
                    const cellX = Math.floor(x);
                    const cellY = Math.floor(y);

                    this.paintCell(cellX, cellY);
                },

                importImage(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    if (!['image/png', 'image/gif'].includes(file.type)) {
                        alert(translations.invalid_file_type);
                        return;
                    }

                    if (!confirm('Import image and overwrite the canvas?')) {
                        e.target.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (event) => {
                        const img = new Image();
                        img.onerror = () => {
                            alert('Invalid image format. Please upload a valid PNG or GIF file.');
                            e.target.value = '';
                        };
                        img.onload = () => {
                            const tempCanvas = document.createElement('canvas');
                            tempCanvas.width = this.cellSideCount;
                            tempCanvas.height = this.cellSideCount;
                            const tempContext = tempCanvas.getContext('2d');
                            tempContext.drawImage(img, 0, 0, this.cellSideCount, this.cellSideCount);

                            // Binarize alpha to avoid semi-transparent pixels
                            const imageData = tempContext.getImageData(0, 0, this.cellSideCount, this.cellSideCount);
                            for (let i = 0; i < imageData.data.length; i += 4) {
                                const a = imageData.data[i + 3];
                                if (a < 128) {
                                    imageData.data[i + 3] = 0;
                                } else {
                                    imageData.data[i + 3] = 255;
                                }
                            }
                            tempContext.putImageData(imageData, 0, 0);

                            // Clear the board
                            this.drawingContext.clearRect(0, 0, this.canvas.width, this.canvas.height);
                            this.colorHistory = {};

                            // Draw the resized image
                            this.drawingContext.drawImage(tempCanvas, 0, 0);

                            // Update colorHistory
                            const importedImageData = this.drawingContext.getImageData(0, 0, this.cellSideCount, this.cellSideCount);
                            for (let y = 0; y < this.cellSideCount; y++) {
                                for (let x = 0; x < this.cellSideCount; x++) {
                                    const index = (y * this.cellSideCount + x) * 4;
                                    const r = importedImageData.data[index];
                                    const g = importedImageData.data[index + 1];
                                    const b = importedImageData.data[index + 2];
                                    const a = importedImageData.data[index + 3];
                                    if (a > 0) {
                                        this.colorHistory[`${x}_${y}`] = this.rgbToHex(r, g, b);
                                    }
                                }
                            }

                            this.updatePreview();
                        };
                        img.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                rgbToHex(r, g, b) {
                    r = Math.round(r);
                    g = Math.round(g);
                    b = Math.round(b);
                    return '#' + ((1 << 24) | (r << 16) | (g << 8) | b).toString(16).slice(1).toUpperCase();
                },

                paintCell(cellX, cellY) {
                    if (this.eraseMode) {
                        this.drawingContext.clearRect(cellX, cellY, 1, 1);
                        delete this.colorHistory[`${cellX}_${cellY}`];
                    } else {
                        this.drawingContext.fillStyle = this.color;
                        this.drawingContext.fillRect(cellX, cellY, 1, 1);
                        this.colorHistory[`${cellX}_${cellY}`] = this.color;
                    }
                    this.updatePreview();
                },

                clearBoard() {
                    if (!confirm('Clear the entire board?')) return;
                    this.drawingContext.clearRect(0, 0, this.canvas.width, this.canvas.height);
                    this.colorHistory = {};
                    this.updatePreview();
                },

                updatePreview() {
                    this.previewContext.clearRect(0, 0, this.previewCanvas.width, this.previewCanvas.height);
                    this.previewContext.drawImage(this.canvas, 0, 0);
                },

                generateGifBlob() {
                    return new Promise((resolve) => {
                        // Get image data to find used colors
                        const imageData = this.drawingContext.getImageData(0, 0, this.canvas.width, this.canvas.height);
                        let usedColors = new Set();
                        for (let y = 0; y < this.canvas.height; y++) {
                            for (let x = 0; x < this.canvas.width; x++) {
                                const idx = (y * this.canvas.width + x) * 4;
                                if (imageData.data[idx + 3] > 0) { // Only consider opaque pixels
                                    const r = imageData.data[idx];
                                    const g = imageData.data[idx + 1];
                                    const b = imageData.data[idx + 2];
                                    usedColors.add((r << 16) | (g << 8) | b); // Store as integer for efficiency
                                }
                            }
                        }

                        // Find an unused color for transparency placeholder, starting from 0xFF00FF
                        let transColor = 0xFF00FF;
                        while (usedColors.has(transColor)) {
                            transColor = (transColor + 1) % 0x1000000; // Increment and wrap around (unlikely to loop much)
                        }
                        const transHex = '#' + transColor.toString(16).padStart(6, '0').toUpperCase();

                        const tempCanvas = document.createElement('canvas');
                        tempCanvas.width = this.canvas.width;
                        tempCanvas.height = this.canvas.height;
                        const tempContext = tempCanvas.getContext('2d');
                        tempContext.fillStyle = transHex; // Use the dynamic placeholder
                        tempContext.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
                        tempContext.drawImage(this.canvas, 0, 0);

                        const gif = new GIF({
                            workers: 2,
                            quality: 10,
                            workerScript: '{{ asset('js/gif/gif.worker.js') }}', // Local worker script
                            width: this.canvas.width,
                            height: this.canvas.height,
                            transparent: transColor // Use the dynamic integer value
                        });

                        gif.addFrame(tempCanvas);

                        gif.on('finished', (blob) => {
                            resolve(blob);
                        });

                        gif.render();
                    });
                },

                async generateCanvas(action) {
                    const blob = await this.generateGifBlob();

                    if (action === 'download') {
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'badge.gif';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                    }
                },

                async buyBadge() {
                    if (Object.keys(this.colorHistory).length === 0 || !this.badgeName.trim() || !this.badgeDescription.trim()) {
                        alert(translations.missing_fields);
                        return;
                    }

                    if (this.badgeName.match(/https?:\/\/|www\./i) || this.badgeDescription.match(/https?:\/\/|www\./i)) {
                        alert(translations.invalid_content);
                        return;
                    }

                    if (!this.canBuy || !confirm(translations.buy_confirmation)) return;

                    this.lastBuyTime = Date.now();
                    this.canBuy = false;
                    this.buttonText = `Cooldown (${Math.ceil((30000 - (Date.now() - this.lastBuyTime)) / 1000)} sec)`;

                    const interval = setInterval(() => {
                        const remainingTime = Math.ceil((30000 - (Date.now() - this.lastBuyTime)) / 1000);
                        if (remainingTime <= 0) {
                            clearInterval(interval);
                            this.canBuy = true;
                            this.buttonText = `{{ __('Buy Badge') }} (${this.cost} ${this.currencyType.charAt(0).toUpperCase() + this.currencyType.slice(1)})`;
                        } else {
                            this.buttonText = `Cooldown (${remainingTime} sec)`;
                        }
                    }, 1000);

                    const blob = await this.generateGifBlob();
                    const reader = new FileReader();
                    reader.onloadend = () => {
                        const base64data = reader.result;

                        fetch('{{ route('badge.buy') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ badge_data: base64data, badge_name: this.badgeName, badge_description: this.badgeDescription })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(translations.purchase_success);
                            } else {
                                alert(data.message || translations.purchase_error_insufficient);
                                clearInterval(interval); // Clear interval if buy fails
                                this.canBuy = true;
                                this.buttonText = `{{ __('Buy Badge') }} (${this.cost} ${this.currencyType.charAt(0).toUpperCase() + this.currencyType.slice(1)})`;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert(translations.purchase_error_general);
                            clearInterval(interval); // Clear interval if buy fails
                            this.canBuy = true;
                            this.buttonText = `{{ __('Buy Badge') }} (${this.cost} ${this.currencyType.charAt(0).toUpperCase() + this.currencyType.slice(1)})`;
                        });
                    };
                    reader.readAsDataURL(blob);

                    // Ensure cooldown ends even if fetch fails or takes long
                    setTimeout(() => {
                        if (!this.canBuy) {
                            clearInterval(interval);
                            this.canBuy = true;
                            this.buttonText = `{{ __('Buy Badge') }} (${this.cost} ${this.currencyType.charAt(0).toUpperCase() + this.currencyType.slice(1)})`;
                        }
                    }, 30000);
                }
            }
        }
    </script>

    <style>
        #canvas {
            cursor: pointer;
        }
        #guide {
            display: grid;
            pointer-events: none;
            position: absolute;
            top: 0;
            left: 0;
        }
        #guide > div {
            box-sizing: border-box;
        }
        .checkerboard {
            background-color: #fff;
            background-image: 
                linear-gradient(45deg, #eee 25%, transparent 25%), 
                linear-gradient(-45deg, #eee 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #eee 75%), 
                linear-gradient(-45deg, transparent 75%, #eee 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }
        .dark .checkerboard {
            background-color: #1a1a1a;
            background-image: 
                linear-gradient(45deg, #2a2a2a 25%, transparent 25%), 
                linear-gradient(-45deg, #2a2a2a 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #2a2a2a 75%), 
                linear-gradient(-45deg, transparent 75%, #2a2a2a 75%);
        }
        input[type="color"] {
            position: absolute;
            top: auto;
            left: auto;
            bottom: 0;
            right: 0;
            transform: translateY(100%);
        }
    </style>
</x-app-layout>