<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About VNFood</title>

    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <!-- about section starts  -->

    <section class="about">

        <div class="row" style="margin-top:100px">

            <div class="image">
                <img src="images/about-img.svg" alt="About VNFood">
            </div>

            <div class="content">
                <h3>Tại sao nên chọn VNFood?</h3>
                <p>VNFood tự hào là thương hiệu đồ ăn nhanh hàng đầu, mang đến hương vị Việt Nam đậm đà kết hợp với
                    phong cách hiện đại. Với nguyên liệu tươi ngon, dịch vụ nhanh chóng và menu đa dạng, chúng tôi cam
                    kết mang lại trải nghiệm tuyệt vời nhất cho khách hàng.</p>
                <a href="menu.php" class="btn">Đặt hàng ngay</a>
            </div>

        </div>

    </section>

    <!-- about section ends -->

    <!-- steps section starts  -->

    <section class="steps">

        <h1 class="title">Phương Châm</h1>

        <div class="box-container">

            <div class="box">
                <img src="images/step-1.png" alt="Choose Your Order">
                <h3>Đặt hàng dễ dàng</h3>
                <p>Dễ dàng chọn món yêu thích từ menu phong phú của chúng tôi, bao gồm burger, pizza, gà rán, và nhiều
                    món khác.</p>
            </div>

            <div class="box">
                <img src="images/step-2.png" alt="Fast Delivery">
                <h3>Giao hàng nhanh</h3>
                <p>Chúng tôi giao hàng nhanh chóng đến tận cửa để bạn có thể thưởng thức món ăn nóng hổi, tươi ngon.</p>
            </div>

            <div class="box">
                <img src="images/step-3.png" alt="Enjoy Your Food">
                <h3>Chất lượng tốt nhất</h3>
                <p>Thưởng thức hương vị tuyệt vời và chia sẻ những khoảnh khắc đáng nhớ cùng gia đình và bạn bè.</p>
            </div>

        </div>

    </section>

    <!-- steps section ends -->

    <!-- reviews section starts  -->

    <section class="reviews">

        <h1 class="title">Đánh giá của khách hàng</h1>

        <div class="swiper reviews-slider">

            <div class="swiper-wrapper">

                <div class="swiper-slide slide">
                    <img src="images/pic-1.png" alt="Customer 1">
                    <p>VNFood thực sự tuyệt vời! Đồ ăn ngon, giao hàng nhanh, và nhân viên rất nhiệt tình.</p>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <h3>Nguyễn Văn A</h3>
                </div>

                <div class="swiper-slide slide">
                    <img src="images/pic-2.png" alt="Customer 2">
                    <p>Đây là địa chỉ ăn nhanh yêu thích của tôi! Hương vị chuẩn Việt Nam, đáng để thử.</p>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Trần Thị B</h3>
                </div>

                <div class="swiper-slide slide">
                    <img src="images/pic-3.png" alt="Customer 3">
                    <p>Tôi rất thích gà của VNFood. Không chỉ ngon mà còn chất lượng hơn cả mong đợi!</p>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Hoàng Minh C</h3>
                </div>

            </div>

            <div class="swiper-pagination"></div>

        </div>

    </section>

    <!-- Facebook Comments Plugin -->
    <div class="fb-comments" data-href="http://yourdom.com/about.php" data-width="100%" data-numposts="100"
        data-colorscheme="light"></div>

    <!-- Load Facebook SDK for JavaScript -->
    <div id="fb-root"></div>

    <!-- reviews section ends -->

    <!-- footer section starts  -->
    <?php include 'components/footer.php'; ?>
    <!-- footer section ends -->

    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId: '1818848021950170',
                xfbml: true,
                version: 'v20.0',
            });
        };

        (function(d, s, id) {
            var js,
                fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = 'https://connect.facebook.net/vi_VN/sdk.js';
            fjs.parentNode.insertBefore(js, fjs);
        })(document, 'script', 'facebook-jssdk');


        var swiper = new Swiper(".reviews-slider", {
            loop: true,
            grabCursor: true,
            spaceBetween: 20,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                0: {
                    slidesPerView: 1,
                },
                700: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
        });
    </script>

</body>

</html>