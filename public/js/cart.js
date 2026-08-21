/**
 * Xử lý giỏ hàng bằng AJAX an toàn (Không bị lỗi JSON khi chuyển hướng hoặc chưa đăng nhập)
 */

// Hàm xử lý an toàn phản hồi từ Server
async function handleServerResponse(response) {
    const text = await response.text();
    // Nếu phản hồi là HTML (do redirect hoặc render HTML)
    if (!text || text.trim().startsWith("<") || text.includes("<!DOCTYPE") || response.redirected) {
        return {
            require_login: true,
            redirect: (typeof BASE_URL !== 'undefined' ? BASE_URL : '/MiniShop_TranThiNgocYen/') + "auth/login",
            message: "Bạn cần phải đăng nhập"
        };
    }
    try {
        return JSON.parse(text);
    } catch (e) {
        return {
            require_login: true,
            redirect: (typeof BASE_URL !== 'undefined' ? BASE_URL : '/MiniShop_TranThiNgocYen/') + "auth/login",
            message: "Bạn cần phải đăng nhập"
        };
    }
}

// 1. Xử lý nút 'Thêm vào giỏ' trên các thẻ sản phẩm
document.querySelectorAll(".btn-add-cart").forEach(button => {
    button.addEventListener("click", function (e) {
        e.preventDefault();
        const productid = this.dataset.productid;
        const appBaseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '/MiniShop_TranThiNgocYen/';

        const formData = new FormData();
        formData.append("productid", productid);
        formData.append("quantity", "1");
        formData.append("is_ajax", "1");
        if (typeof CSRF_TOKEN !== 'undefined') {
            formData.append("csrf_token", CSRF_TOKEN);
        }

        fetch(appBaseUrl + "cart/add", {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            },
            body: formData
        })
        .then(res => handleServerResponse(res))
        .then(data => {
            if (!data) return;

            if (data.require_login) {
                alert(data.message || "Bạn cần phải đăng nhập");
                window.location.href = data.redirect || (appBaseUrl + "auth/login");
                return;
            }

            if (data.success) {
                const cartCountEl = document.querySelector("#cartCount");
                if (cartCountEl) {
                    cartCountEl.textContent = data.cartCount;
                }

                const toastMessage = document.querySelector("#toastMessage");
                if (toastMessage) {
                    toastMessage.textContent = data.message;
                }
                const liveToast = document.querySelector("#liveToast");
                if (liveToast && typeof bootstrap !== 'undefined') {
                    const toast = new bootstrap.Toast(liveToast);
                    toast.show();
                }
            } else {
                alert(data.message || "Không thể thêm vào giỏ hàng!");
            }
        })
        .catch(error => {
            console.error("Lỗi:", error);
            alert("Bạn cần phải đăng nhập");
            window.location.href = appBaseUrl + "auth/login";
        });
    });
});

// 2. Cập nhật số lượng sản phẩm trong giỏ
function updateCart(productid, quantity) {
    if (quantity < 1) {
        return;
    }

    const appBaseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '/MiniShop_TranThiNgocYen/';
    const formData = new FormData();
    formData.append("productid", productid);
    formData.append("quantity", quantity);
    formData.append("is_ajax", "1");
    if (typeof CSRF_TOKEN !== 'undefined') {
        formData.append("csrf_token", CSRF_TOKEN);
    }

    fetch(appBaseUrl + "cart/update", {
        method: "POST",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json"
        },
        body: formData
    })
    .then(res => handleServerResponse(res))
    .then(data => {
        if (!data) return;

        if (data.require_login) {
            alert(data.message || "Bạn cần phải đăng nhập");
            window.location.href = data.redirect || (appBaseUrl + "auth/login");
            return;
        }

        if (data.success) {
            const cartCountEl = document.querySelector("#cartCount");
            if (cartCountEl) {
                cartCountEl.textContent = data.cartCount;
            }

            const itemSubtotalEl = document.querySelector(`#subtotal-${productid}`);
            if (itemSubtotalEl) {
                itemSubtotalEl.textContent = new Intl.NumberFormat('vi-VN').format(data.itemSubtotal) + " đ";
            }

            const totalEl = document.querySelector("#cartTotal");
            if (totalEl) {
                totalEl.textContent = new Intl.NumberFormat('vi-VN').format(data.total) + " đ";
            }

            const qtyInput = document.querySelector(`input[name="quantity[${productid}]"]`);
            if (qtyInput) {
                qtyInput.value = data.newQuantity;
            }
        } else {
            alert(data.message || "Lỗi cập nhật giỏ hàng!");
        }
    })
    .catch(error => {
        console.error("Lỗi:", error);
    });
}

// 3. Xóa sản phẩm khỏi giỏ
function removeCart(productid) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng không?')) {
        return;
    }

    const appBaseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '/MiniShop_TranThiNgocYen/';
    const formData = new FormData();
    formData.append("productid", productid);
    formData.append("is_ajax", "1");
    if (typeof CSRF_TOKEN !== 'undefined') {
        formData.append("csrf_token", CSRF_TOKEN);
    }

    fetch(appBaseUrl + "cart/remove", {
        method: "POST",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json"
        },
        body: formData
    })
    .then(res => handleServerResponse(res))
    .then(data => {
        if (!data) return;

        if (data.success) {
            const cartCountEl = document.querySelector("#cartCount");
            if (cartCountEl) {
                cartCountEl.textContent = data.cartCount;
            }

            if (data.isCartEmpty) {
                location.reload(); 
            } else {
                const row = document.querySelector(`#row-${productid}`);
                if (row) {
                    row.remove();
                }

                const totalEl = document.querySelector("#cartTotal");
                if (totalEl) {
                    totalEl.textContent = new Intl.NumberFormat('vi-VN').format(data.total) + " đ";
                }
            }
        } else {
            alert(data.message || "Không thể xóa sản phẩm!");
        }
    })
    .catch(error => {
        console.error("Lỗi:", error);
    });
}

// 4. Xử lý form thêm vào giỏ hàng trên trang chi tiết sản phẩm
const detailCartForm = document.querySelector("#productDetailCartForm");
if (detailCartForm) {
    detailCartForm.addEventListener("submit", function (e) {
        if (typeof IS_LOGGED_IN !== 'undefined' && !IS_LOGGED_IN) {
            e.preventDefault();
            const productid = this.querySelector('input[name="product_id"]')?.value || this.querySelector('input[name="productid"]')?.value;
            const quantity = this.querySelector('input[name="quantity"]')?.value || 1;
            const appBaseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '/MiniShop_TranThiNgocYen/';

            const formData = new FormData();
            formData.append("product_id", productid);
            formData.append("quantity", quantity);
            formData.append("is_ajax", "1");
            if (typeof CSRF_TOKEN !== 'undefined') {
                formData.append("csrf_token", CSRF_TOKEN);
            }

            fetch(appBaseUrl + "cart/add", {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                },
                body: formData
            })
            .then(res => handleServerResponse(res))
            .then(data => {
                alert(data.message || "Bạn cần phải đăng nhập");
                window.location.href = data.redirect || (appBaseUrl + "auth/login");
            })
            .catch(() => {
                alert("Bạn cần phải đăng nhập");
                window.location.href = appBaseUrl + "auth/login";
            });
        }
    });
}

// 5. Xử lý Thả Tim Yêu Thích (Wishlist Toggle)
document.addEventListener("click", function (e) {
    const btn = e.target.closest(".btn-wishlist-toggle");
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    const productId = btn.dataset.productId || btn.dataset.productid;
    if (!productId) return;

    const appBaseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '/MiniShop_TranThiNgocYen/';
    const formData = new FormData();
    formData.append("product_id", productId);
    if (typeof CSRF_TOKEN !== 'undefined') {
        formData.append("csrf_token", CSRF_TOKEN);
    }

    fetch(appBaseUrl + "wishlist/toggle", {
        method: "POST",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.success) {
            // Cập nhật tất cả các nút tim có cùng productId trên trang
            document.querySelectorAll(`.btn-wishlist-toggle[data-product-id="${productId}"], .btn-wishlist-toggle[data-productid="${productId}"]`).forEach(el => {
                const icon = el.querySelector("i");
                if (data.is_favorite) {
                    el.classList.add("active");
                    el.title = "Bỏ yêu thích";
                    if (icon) {
                        icon.className = "bi bi-heart-fill";
                    }
                } else {
                    el.classList.remove("active");
                    el.title = "Thêm vào yêu thích";
                    if (icon) {
                        icon.className = "bi bi-heart";
                    }
                }
            });

            // Cập nhật badge số lượng ở header
            const headerBadge = document.getElementById("wishlistCount");
            if (headerBadge) {
                const count = data.count || 0;
                headerBadge.textContent = count;
                if (count > 0) {
                    headerBadge.classList.remove("d-none");
                } else {
                    headerBadge.classList.add("d-none");
                }
            }

            // Hiện thông báo toast
            const toastEl = document.getElementById("liveToast");
            const toastMsg = document.getElementById("toastMessage");
            if (toastEl && toastMsg && typeof bootstrap !== 'undefined') {
                toastMsg.textContent = data.message;
                const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
                toast.show();
            }
        }
    })
    .catch(err => {
        console.error("Lỗi Wishlist toggle:", err);
    });
});