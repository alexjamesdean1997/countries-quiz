export function stringNormalizer(string) {
    return string
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replaceAll('-', ' ')
        .replaceAll('\'', ' ')
        .replaceAll('’', ' ')
        .toLowerCase();
}