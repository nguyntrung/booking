<?php
   include '../database/db.php';


   session_start(); 
   $email = isset($_SESSION['userEmail']) ? $_SESSION['userEmail'] : '';
?>
<div class="promos" style="max-width: 800px; margin: 0 auto;">
    <!-- Promotions Section -->
    <section class="promotions p-3 mb-3 rounded-4">
        <div class="container">
            <h2 class="promotion-title">Phản hồi</h2>
            <div class="btnstar mb-2" style="margin: auto; max-width: 300px">
                <button type="button" class="btn btn-outline-warning star-btn m-2" data-value="1">&star;</button>
                <button type="button" class="btn btn-outline-warning star-btn m-2" data-value="2">&star;</button>
                <button type="button" class="btn btn-outline-warning star-btn m-2" data-value="3">&star;</button>
                <button type="button" class="btn btn-outline-warning star-btn m-2" data-value="4">&star;</button>
                <button type="button" class="btn btn-outline-warning star-btn m-2" data-value="5">&star;</button>
            </div>
            <div class="form-floating">
                <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 200px"></textarea>
                <label for="floatingTextarea2">Phản hồi</label>
            </div>
            <div class="btn" style="width: 100%;">
                <button id="submitRatingBtn" class="btn btn-primary mt-3 rounded-pill" style="margin: auto">Gửi phản hồi</button>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    let selectedRating = 0;

    const starButtons = document.querySelectorAll('.star-btn');

    starButtons.forEach(button => {
        button.addEventListener('mouseover', function () {
            const value = this.getAttribute('data-value');
            starButtons.forEach(star => {
                if (star.getAttribute('data-value') <= value) {
                    star.classList.add('btn-warning');
                    star.classList.remove('btn-outline-warning');
                } else {
                    star.classList.remove('btn-warning');
                    star.classList.add('btn-outline-warning');
                }
            });
        });

        button.addEventListener('mouseout', function () {
            if (selectedRating === 0) {
                starButtons.forEach(star => {
                    star.classList.remove('btn-warning');
                    star.classList.add('btn-outline-warning');
                });
            } else {
                starButtons.forEach(star => {
                    if (star.getAttribute('data-value') <= selectedRating) {
                        star.classList.add('btn-warning');
                        star.classList.remove('btn-outline-warning');
                    } else {
                        star.classList.remove('btn-warning');
                        star.classList.add('btn-outline-warning');
                    }
                });
            }
        });
    });

    starButtons.forEach(button => {
        button.addEventListener('click', function () {
            selectedRating = this.getAttribute('data-value');

            starButtons.forEach(star => {
                if (star.getAttribute('data-value') <= selectedRating) {
                    star.classList.add('btn-warning');
                    star.classList.remove('btn-outline-warning');
                } else {
                    star.classList.remove('btn-warning');
                    star.classList.add('btn-outline-warning');
                }
            });
        });
    });

    document.getElementById('submitRatingBtn').addEventListener('click', function () {
        const feedback = document.getElementById('floatingTextarea2').value; 

        if (selectedRating === 0) {
            alert('Vui lòng chọn số sao!');
            return;
        }

        if (!feedback) {
            alert('Vui lòng nhập phản hồi của bạn!');
            return;
        }

        const formData = new FormData();
        formData.append('rating', selectedRating);
        formData.append('feedback', feedback);

        fetch('submit_rating.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                selectedRating = 0;
                document.getElementById('floatingTextarea2').value = '';
                starButtons.forEach(star => {
                    star.classList.remove('btn-warning');
                    star.classList.add('btn-outline-warning');
                });
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi gửi phản hồi!');
        });
    });
});
</script>