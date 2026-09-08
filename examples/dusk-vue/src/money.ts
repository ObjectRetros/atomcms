import type { Data } from "./api";
export function money(value?: Data<"Money"> | null): string {
    if (!value) return "";
    const formatter = new Intl.NumberFormat(undefined, {
        style: "currency",
        currency: value.currency,
    });
    const decimals = formatter.resolvedOptions().maximumFractionDigits ?? 2;
    return formatter.format(Number(value.amount_minor) / 10 ** decimals);
}
