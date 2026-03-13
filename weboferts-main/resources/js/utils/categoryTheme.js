const THEMES = {
  camba: { icon: 'mdi-food-variant', gradient: 'linear-gradient(135deg, #ff9966 0%, #ff5e62 100%)', accent: '#ff7b54', shadow: 'rgba(255, 94, 98, 0.34)' },
  pastas: { icon: 'mdi-pasta', gradient: 'linear-gradient(135deg, #f6d365 0%, #fda085 100%)', accent: '#f39b6d', shadow: 'rgba(253, 160, 133, 0.32)' },
  parrilla: { icon: 'mdi-grill', gradient: 'linear-gradient(135deg, #434343 0%, #c96f52 100%)', accent: '#d98159', shadow: 'rgba(120, 66, 47, 0.34)' },
  pizza: { icon: 'mdi-pizza', gradient: 'linear-gradient(135deg, #f83600 0%, #f9d423 100%)', accent: '#ff8a3d', shadow: 'rgba(248, 54, 0, 0.32)' },
  hamburguesa: { icon: 'mdi-hamburger', gradient: 'linear-gradient(135deg, #c79081 0%, #dfa579 100%)', accent: '#d39a73', shadow: 'rgba(171, 110, 88, 0.3)' },
  pollo: { icon: 'mdi-food-drumstick', gradient: 'linear-gradient(135deg, #f7971e 0%, #ffd200 100%)', accent: '#f2b11f', shadow: 'rgba(247, 151, 30, 0.32)' },
  'almuerzo-familiar': { icon: 'mdi-food-fork-drink', gradient: 'linear-gradient(135deg, #43cea2 0%, #185a9d 100%)', accent: '#3fc2a2', shadow: 'rgba(24, 90, 157, 0.34)' },
  rapida: { icon: 'mdi-run-fast', gradient: 'linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%)', accent: '#fb8f2e', shadow: 'rgba(252, 74, 26, 0.32)' },
  helados: { icon: 'mdi-ice-cream', gradient: 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)', accent: '#d89fe1', shadow: 'rgba(188, 132, 223, 0.3)' },
  default: { icon: 'mdi-label-outline', gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', accent: '#7c62c7', shadow: 'rgba(118, 75, 162, 0.3)' },
};

export function normalizeCategoryKey(value) {
  return (value || '')
    .toString()
    .trim()
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[ _]+/g, '-')
    .replace(/[^a-z0-9-]/g, '');
}

export function getCategoryTheme(value) {
  const key = normalizeCategoryKey(value);
  return THEMES[key] || THEMES.default;
}