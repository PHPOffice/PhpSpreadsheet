Array.from(document.styleSheets).forEach((sheet) => {
    if (sheet.href?.includes('darkmode.css') ?? false) {
        sheet.disabled = !sheet.disabled;
    }
});
