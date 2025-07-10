interface Window {
  Intl: typeof Intl;
}

declare namespace Intl {
  interface DateTimeFormatOptions {
    timeZone?: string;
    hour?: "2-digit" | "numeric";
    minute?: "2-digit" | "numeric";
    second?: "2-digit" | "numeric";
    hour12?: boolean;
  }
} 