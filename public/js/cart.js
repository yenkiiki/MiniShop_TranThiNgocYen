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