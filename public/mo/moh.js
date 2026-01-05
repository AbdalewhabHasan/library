document.addEventListener("DOMContentLoaded", function() {
    // CSRF Token for AJAX requests
    const csrfToken = document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content");

    // Function to show a notification
    function showNotification(message, type = "success") {
        const notification = document.createElement("div");
        notification.className = "notification notification-" + type;
        notification.innerHTML = '<i class="fas fa-info-circle"></i> ' + message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add("show");
        }, 100);

        setTimeout(() => {
            notification.classList.remove("show");
            notification.addEventListener("transitionend", () => notification.remove());
        }, 3000);
    }

    // --- Rating Functionality ---
    document.querySelectorAll(".rating-stars .fa-star").forEach(star => {
        star.addEventListener("click", function() {
            const audioBookId = this.closest(".rating-stars").dataset.audiobookId;
            const rating = this.dataset.rating;

            fetch("/listener/rate/" + audioBookId, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ rating: rating })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Rating response data:", data);
                if (data.success) {
                    showNotification(data.message, "success");
                    const starsContainer = this.closest(".rating-stars");
                    starsContainer.querySelectorAll(".fa-star").forEach(s => {
                        s.classList.toggle("text-warning", parseInt(s.dataset.rating) <= data.newRating);
                        s.classList.toggle("text-muted", parseInt(s.dataset.rating) > data.newRating);
                    });

                    // إظهار زر حذف التقييم فورًا بعد التقييم (إذا كان موجودًا في DOM)
                    const removeButton = document.querySelector(`.remove-rating-btn[data-audiobook-id="${audioBookId}"]`);
                    if (removeButton) {
                        removeButton.style.display = "inline-block";
                        console.log("Remove button found and displayed.");
                    } else {
                        console.log("Remove button not found in DOM after rating.");
                    }

                } else {
                    showNotification(data.message, "danger");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showNotification("حدث خطأ أثناء التقييم.", "danger");
            });
        });
    });

    // --- Remove Rating Functionality ---
    document.querySelectorAll(".remove-rating-btn").forEach(button => {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            const audioBookId = this.dataset.audiobookId;

            if (confirm("هل أنت متأكد أنك تريد حذف تقييمك؟")) {
                fetch("/remove-rating/" + audioBookId, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    }
                })
                .then(response => {
                    // Check if the response is JSON before parsing
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json();
                    } else {
                        // If not JSON, return a generic error or handle as needed
                        throw new TypeError("Oops, we haven't got JSON!");
                    }
                })
                .then(data => {
                    console.log("Remove Rating response data:", data);
                    if (data.success) {
                        showNotification(data.message, "success");
                        const starsContainer = this.closest("[data-audiobook-id]").querySelector(".rating-stars");
                        starsContainer.querySelectorAll(".fa-star").forEach(s => {
                            s.classList.remove("text-warning");
                            s.classList.add("text-muted");
                        });
                        this.remove();
                        console.log("Remove button removed from DOM.");
                    } else {
                        showNotification(data.message, "danger");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    showNotification("حدث خطأ أثناء حذف التقييم.", "danger");
                });
            }
        });
    });

    // --- Download Functionality ---
    document.querySelectorAll(".download-btn").forEach(button => {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            const audioBookId = this.dataset.audiobookId;
            const downloadUrl = "/listener/download/" + audioBookId;

            window.location.href = downloadUrl;
        });
    });
});


