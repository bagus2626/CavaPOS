document.addEventListener("DOMContentLoaded", function () {

    // Filter kategori
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            let category = this.dataset.category;

            document.querySelectorAll('.filter-btn').forEach(b =>
                b.classList.remove('active')
            );
            this.classList.add('active');

            document.querySelectorAll('.menu-item').forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = "flex";
                } else {
                    item.style.display = "none";
                }
            });
        });
    });

    //hide category_name yang tidak perlu dimunculkan saat sebuah category dipilih
    const filterButtons = document.querySelectorAll('.filter-btn');
    const categoryGroups = document.querySelectorAll('.category-group');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const selectedCategory = btn.dataset.category;

            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            categoryGroups.forEach(group => {
                if(selectedCategory === 'all') {
                    group.style.display = 'block';
                } else {
                    if(group.dataset.category === selectedCategory){
                        group.style.display = 'block';
                    } else {
                        group.style.display = 'none';
                    }
                }
            });
        });
    });

});
