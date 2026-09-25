const foods = [
    { id: 1, name: 'Classic Burger', category: 'Burger', price: 250, icon: '🍔', desc: 'Juicy beef burger with fresh vegetables.' },
    { id: 2, name: 'Cheese Pizza', category: 'Pizza', price: 450, icon: '🍕', desc: 'Hot pizza topped with extra cheese.' },
    { id: 3, name: 'Chicken Burger', category: 'Burger', price: 300, icon: '🍔', desc: 'Crispy chicken with special sauce.' },
    { id: 4, name: 'Cold Coffee', category: 'Drinks', price: 150, icon: '🥤', desc: 'Refreshing chilled coffee.' },
    { id: 5, name: 'Pepperoni Pizza', category: 'Pizza', price: 550, icon: '🍕', desc: 'Classic pepperoni with mozzarella.' },
    { id: 6, name: 'Fresh Juice', category: 'Drinks', price: 120, icon: '🧃', desc: 'Fresh and healthy seasonal juice.' }
];
let selected = 'all';
const grid = document.getElementById('foodGrid');
function getCart() { return JSON.parse(localStorage.getItem('foodieCart') || '[]') }
function saveCart(c) { localStorage.setItem('foodieCart', JSON.stringify(c)); updateCount() }
function updateCount() { const el = document.getElementById('cartCount'); if (el) el.textContent = getCart().reduce((s, i) => s + i.qty, 0) }
function render() { if (!grid) return; let q = (document.getElementById('search')?.value || '').toLowerCase(); grid.innerHTML = foods.filter(f => (selected === 'all' || f.category === selected) && f.name.toLowerCase().includes(q)).map(f => `<article class="food-card"><div class="food-icon">${f.icon}</div><div class="food-info"><h3>${f.name}</h3><p>${f.desc}</p><div class="food-bottom"><span class="price">৳${f.price}</span><button class="add" onclick="addToCart(${f.id})">+ Add</button></div></div></article>`).join('') }
function addToCart(id) { let c = getCart(), item = c.find(i => i.id === id); item ? item.qty++ : c.push({ ...foods.find(f => f.id === id), qty: 1 }); saveCart(c); alert('Food added to cart!') }
document.querySelectorAll('.filter').forEach(b => b.onclick = () => { document.querySelectorAll('.filter').forEach(x => x.classList.remove('active')); b.classList.add('active'); selected = b.dataset.category; render() });
document.getElementById('search')?.addEventListener('input', render); render(); updateCount();



// =========================
// PROFILE DROPDOWN
// =========================

function toggleProfileMenu() {

    const dropdown = document.getElementById('profileDropdown');

    if (dropdown) {
        dropdown.classList.toggle('show');
    }

}

window.addEventListener('click', function (event) {

    if (!event.target.closest('.profile-menu')) {

        const dropdown = document.getElementById('profileDropdown');

        if (dropdown) {
            dropdown.classList.remove('show');
        }

    }

});