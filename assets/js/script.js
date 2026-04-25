document.addEventListener("DOMContentLoaded", () => {
    const addButtons = document.querySelectorAll(".btn-add");
    const cartBadge = document.querySelector(".cart-badge");

    let cartCount = parseInt(cartBadge.innerText);

    addButtons.forEach(button => {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            cartCount++;
            cartBadge.innerText = cartCount;

            // Micro animation
            const originalHTML = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-check"></i>';
            this.style.backgroundColor = "var(--primary-color)";
            this.style.color = "white";

            // Ripple/Scale effect on cart icon
            const cartIcon = document.querySelector(".cart-icon");
            if (cartIcon) {
                cartIcon.style.transform = "scale(1.2)";
                setTimeout(() => {
                    cartIcon.style.transform = "scale(1)";
                }, 200);
            }

            setTimeout(() => {
                this.innerHTML = originalHTML;
                this.style.backgroundColor = "var(--white)";
                this.style.color = "var(--primary-color)";
            }, 1500);
        });
    });

    // Smooth scroll functionality could go here
});
