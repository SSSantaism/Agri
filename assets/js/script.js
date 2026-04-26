document.addEventListener("DOMContentLoaded", () => {
    const addButtons = document.querySelectorAll(".btn-add");
    const cartBadge = document.querySelector(".cart-badge");

    addButtons.forEach(button => {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            
            const productId = this.getAttribute('data-product-id');
            if (!productId) return;
            
            const btn = this;
            const originalHTML = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            btn.disabled = true;
            
            fetch('/Agri/api/cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=add&product_id=' + productId + '&quantity=1'
            })
            .then(r => r.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                
                if (data.success) {
                    // Success animation
                    btn.innerHTML = '<i class="fa-solid fa-check"></i>';
                    btn.style.backgroundColor = "var(--primary-color)";
                    btn.style.color = "white";
                    
                    // Update cart badge
                    if (cartBadge) {
                        cartBadge.innerText = data.cart_count;
                    }
                    
                    // Ripple effect on cart icon
                    const cartIcon = document.querySelector(".cart-icon");
                    if (cartIcon) {
                        cartIcon.style.transform = "scale(1.2)";
                        setTimeout(() => {
                            cartIcon.style.transform = "scale(1)";
                        }, 200);
                    }
                    
                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                        btn.style.backgroundColor = "var(--white)";
                        btn.style.color = "var(--primary-color)";
                        btn.disabled = false;
                    }, 1500);
                } else {
                    alert(data.message);
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error('Cart error:', err);
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            });
        });
    });
});
