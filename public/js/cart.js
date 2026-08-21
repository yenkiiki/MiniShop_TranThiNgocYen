document.querySelectorAll(".btn-add-cart").forEach(button => {
    button.addEventListener("click", function () {
        const productid = this.dataset.productid;
        const formData = new FormData();
        formData.append("productid", productid);
        formData.append("csrf_token", CSRF_TOKEN);

        fetch(BASE_URL + "cart/add", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật số lượng trên Header không cần reload trang
                document.querySelector("#cartCount").textContent = data.cartCount;

                // Hiển thị thông báo bằng Bootstrap Toast
                const toastMessage = document.querySelector("#toastMessage");
                if (toastMessage) {
                    toastMessage.textContent = data.message;
                }
                const liveToast = document.querySelector("#liveToast");
                if (liveToast) {
                    const toast = new bootstrap.Toast(liveToast);
                    toast.show();
                }
            } else {
                alert(data.message);
            }
            console.log(data);
        })
        .catch(error => {
            console.error("Lỗi:", error);
        });
    });
});
function updateCart(productid, quantity) {
    if (quantity < 1) {
        return;
    }

    const formData = new FormData();
    formData.append("productid", productid);
    formData.append("quantity", quantity);
    formData.append("csrf_token", CSRF_TOKEN);

    fetch(BASE_URL + "cart/update", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector("#cartCount").textContent = data.cartCount;

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
            alert(data.message);
        }
    })
    .catch(error => {
        console.error("Lỗi:", error);
    });
}
function removeCart(productid) {
    if (!confirm('Má có chắc muốn xóa sản phẩm này khỏi giỏ hàng không?')) {
        return;
    }

    const formData = new FormData();
    formData.append("productid", productid);
    formData.append("csrf_token", CSRF_TOKEN);

    fetch(BASE_URL + "cart/remove", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
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
            alert(data.message);
        }
    })
    .catch(error => {
        console.error("Lỗi:", error);
    });
}