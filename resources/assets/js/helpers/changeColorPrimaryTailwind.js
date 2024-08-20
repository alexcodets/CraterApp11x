const hexToRgb = string => {
    // get the r,g,b values
    const [r, g, b] = string
        .replace('#', '')
        .match(/.{1,2}/g)
        .map(a => parseInt(a, 16));

    return { r, g, b };
};

const toHex = n => `0${n.toString(16)}`.slice(-2).toUpperCase();

const rgbToHex = (r, g, b) => `#${toHex(r)}${toHex(g)}${toHex(b)}`;

const lighten = (hex, intensity) => {
    // get r, g, b values
    let { r, g, b } = hexToRgb(hex);

    // lighten the r, g, b values
    r = Math.round(r + (255 - r) * intensity);
    g = Math.round(g + (255 - g) * intensity);
    b = Math.round(b + (255 - b) * intensity);
    // return the new hex color
    return rgbToHex(r, g, b);
};

const changeColorPrimaryTailwind = (color = null) => {
    if (color === null) {
        color = localStorage.getItem('primary_color')
    }

    if (color) {
        const colors = {
            50: lighten(color, 0.95),
            100: lighten(color, 0.9),
            200: lighten(color, 0.75),
            300: lighten(color, 0.6),
            400: lighten(color, 0.3),
            500: lighten(color, 0),
            600: lighten(color, 0.1),
            700: lighten(color, 0.25),
            800: lighten(color, 0.4),
            900: lighten(color, 0.51)
        }
        const root = document.documentElement.style
        for (const key in colors) {
            root.setProperty(`--primary-color-${key}`, colors[key])
        }
    }
}

export default changeColorPrimaryTailwind