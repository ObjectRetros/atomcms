import { GIFEncoder, quantize, applyPalette } from "gifenc";

export function badgeGif(pixels: Uint8ClampedArray): Uint8Array {
    const palette = quantize(pixels, 256, {
        format: "rgba4444",
        oneBitAlpha: true,
    });
    const indexed = applyPalette(pixels, palette, "rgba4444");
    const transparentIndex = palette.findIndex((color) => color[3] === 0);
    const gif = GIFEncoder();
    gif.writeFrame(indexed, 40, 40, {
        palette,
        transparent: transparentIndex >= 0,
        transparentIndex: Math.max(0, transparentIndex),
        repeat: -1,
    });
    gif.finish();
    return gif.bytes();
}
