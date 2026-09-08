export function displayDate(
    value: string | null | undefined,
    time = false
): string {
    if (!value) return "";
    const date = new Date(value);
    return time ? date.toLocaleString() : date.toLocaleDateString();
}
