<?php

class Api
{
    public $blogName;
    protected $response;

    public function __construct($blogName)
    {
        $this->blogName = $blogName;
    }

    public function getBaseUrl()
    {
        return 'https://akrez.ir/api';
        return 'http://localhost/akrez5/api';
    }

    public function getGalleryUrl($name)
    {
        return 'https://akrezing.ir/gallery/' . $name;
    }

    public function callApi()
    {
        if (null !== $this->response) {
            return $this->response;
        }

        $url = $this->getBaseUrl() . '/' . $this->blogName;

        $curl = curl_init($url);
        curl_setopt_array($curl, [ 
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);
        $result = curl_exec($curl);
        curl_close($curl);

        return $this->response = (array)json_decode($result, true);
    }

    public function getItem($index)
    {
        $items = $this->callApi();
        return $items[$index];
    }

    public function getKeyValues($index, $modelId = null)
    {
        $result = [];
        foreach ($this->getItem($index) as $item) {
            if ($modelId === null or $modelId == $item['model_id']) {
                $result[] = $item;
            }
        }
        return $result;
    }

    public function getValues($index, $modelId = null)
    {
        $result = [];
        foreach ($this->getItem($index) as $item) {
            if ($modelId === null or $modelId == $item['model_id']) {
                $result = array_merge($result, $item['values']);
            }
        }
        return $result;
    }

    public function getNameUrls($index, $modelId = null)
    {
        $result = [];
        foreach ($this->getItem($index) as $item) {
            if ($modelId === null or $modelId == $item['model_id']) {
                foreach ($item['names'] as $name) {
                    $result[] = $this->getGalleryUrl($name);
                }
            }
        }
        return $result;
    }

    public function getFirstNameUrl($index, $modelId = null)
    {
        $urls = $this->getNameUrls($index, $modelId);
        if ($urls) {
            return reset($urls);
        }
        return null;
    }

    public function getBlog($index)
    {
        $blog = $this->getItem('blog');
        return $blog[$index];
    }
}

$api = new Api('shahabtahrir');

$contacts = $api->getItem('contacts');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= $api->getBlog('title') ?> | <?= $api->getBlog('slug') ?></title>

    <meta name="keywords" content="<?= implode(',', $api->getValues('blog_keywords')) ?>">
    <meta name="description" content="<?= $api->getBlog('description') ?>">

    <!-- Favicons -->
    <link href="<?= $api->getFirstNameUrl('blog_logos') ?>" rel="icon" />
    <link href="<?= $api->getFirstNameUrl('blog_logos') ?>" rel="apple-touch-icon" />

    <style>
        #hero {
            background: url("<?= $api->getFirstNameUrl('blog_heros') ?>") top right no-repeat;
        }
    </style>

    <!-- Vendor CSS Files -->
    <link href="node_modules/aos/dist/aos.css" rel="stylesheet" />
    <link href="node_modules/bootstrap/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <link href="node_modules/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="node_modules/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <link href="node_modules/glightbox/dist/css/glightbox.min.css" rel="stylesheet" />
    <link href="node_modules/swiper/swiper-bundle.min.css" rel="stylesheet" />

    <!-- Template Main CSS File -->
    <link href="assets/MyResume/css/style.css" rel="stylesheet" />
    <link href="css/font-sahel.css" rel="stylesheet" />
</head>

<body dir="rtl">
    <!-- ======= Mobile nav toggle button ======= -->
    <!-- <button type="button" class="mobile-nav-toggle d-xl-none"><i class="bi bi-list mobile-nav-toggle"></i></button> -->
    <i class="bi bi-list mobile-nav-toggle d-lg-none"></i>
    <!-- ======= Header ======= -->
    <header id="header" class="d-flex flex-column justify-content-center">
        <nav id="navbar" class="navbar nav-menu">
            <ul dir="ltr">
                <li>
                    <a href="#hero" class="nav-link scrollto active"><i class="bx bx-home"></i> <span><?= $api->getBlog('title') ?></span></a>
                </li>
                <li>
                    <a href="#portfolio" class="nav-link scrollto"><i class="bx bx-book-content"></i>
                        <span>محصولات <?= $api->getBlog('title') ?></span></a>
                </li>
                <?php if ($contacts) { ?>
                    <li>
                        <a href="#contact" class="nav-link scrollto"><i class="bx bx-envelope"></i> <span>ارتباط با ما</span></a>
                    </li>
                <?php } ?>
                <li>
                    <a href="#footer" class="nav-link scrollto"><i class="bx bx-user"></i> <span>درباره ما</span></a>
                </li>
            </ul>
        </nav>
        <!-- .nav-menu -->
    </header>
    <!-- End Header -->

    <!-- ======= Hero Section ======= -->
    <section id="hero" class="d-flex flex-column justify-content-center">
        <div class="container" data-aos="zoom-in" data-aos-delay="100">
            <h1><?= $api->getBlog('title') ?> <small class="text-muted"><?= $api->getBlog('slug') ?></small></h1>
        </div>
    </section>
    <!-- End Hero -->

    <main id="main">

        <!-- ======= Portfolio Section ======= -->
        <section id="portfolio" class="portfolio section-bg">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2>محصولات <?= $api->getBlog('title') ?></h2>
                </div>

                <div class="row">
                    <div class="col-lg-12 d-flex justify-content-center" data-aos="fade-up" data-aos-delay="100">
                        <ul id="portfolio-flters">
                            <li data-filter="*" class="filter-active w-100"><strong>همه محصولات <?= $api->getBlog('title') ?></strong></li>
                            <?php
                            foreach ($api->getValues('blog_categories') as $blogCategory) {
                                echo '<li data-filter=".filter-' . crc32($blogCategory) . '"><strong>' . $blogCategory . '</strong></li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>

                <div class="row portfolio-container" data-aos="fade-up" data-aos-delay="200">
                    <?php
                    foreach ($api->getItem('products') as $product) {
                        $images = $api->getNameUrls('products_images', $product['id']);
                        if (empty($images)) {
                            $images = [$api->getFirstNameUrl('blog_logos')];
                        }
                    ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 portfolio-item <?php foreach ($api->getValues('products_categories', $product['id']) as $tag) echo 'filter-' . crc32($tag); ?>">
                            <div class="portfolio-wrap text-center">
                                <?php
                                echo '<span class="d-inline-block p-3">' . $product['title'] . '</span>';
                                if ($images) {
                                    echo '<img src="' . $api->getFirstNameUrl('products_images', $product['id']) . '" class="img-fluid" alt="' . htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') . '" />';
                                }
                                ?>
                                <div class="portfolio-info">
                                    <h4><?= $product['title'] ?></h4>
                                    <p>
                                        <?php
                                        foreach ($api->getKeyValues('products_properties', $product['id']) as $productProperties) {
                                            echo '<strong>' . $productProperties['key'] . ':</strong> ';
                                            echo implode(', ', $productProperties['values']);
                                            echo '<br>';
                                        }
                                        ?>
                                    </p>
                                    <div class="portfolio-links">
                                        <?php
                                        foreach ($images as $image) {
                                            echo '<a href="' . $image . '" data-gallery="portfolioGallery" class="portfolio-lightbox" title="' . $product['title'] . '"><i class="bx bx-plus"></i></a>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
        <!-- End Portfolio Section -->

        <!-- ======= Contact Section ======= -->
        <?php if ($contacts) { ?>
            <section id="contact" class="contact">
                <div class="container" data-aos="fade-up">
                    <div class="section-title">
                        <h2>ارتباط با ما</h2>
                    </div>
                    <div class="row">
                        <?php
                        $contactSize = max(3, intval(12 / count($contacts)));
                        foreach ($contacts as $contact) {
                            if ('email' == $contact['contact_type']) {
                                $icon = 'envelope';
                            } elseif ('phone' == $contact['contact_type']) {
                                $icon = 'telephone';
                            } elseif ('address' == $contact['contact_type']) {
                                $icon = 'geo-alt';
                            } else {
                                $icon = $contact['contact_type'];
                            }
                        ?>
                            <div class="col-lg-<?= $contactSize ?> pt-3">
                                <div class="info text-center">
                                    <div class="address d-inline-block">
                                        <i class="bi bi-<?= $icon ?>"></i>
                                        <div class="h4"><?= $contact['title'] ?></div>
                                        <p><a href="<?= $contact['link'] ?>" dir="ltr"><?= $contact['content'] ?></a></p>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }

                        ?>
                    </div>
                </div>
            </section>
        <?php } ?>
        <!-- End Contact Section -->
    </main>
    <!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="container">
            <h3><?= $api->getBlog('title') ?></h3>
            <p><?= $api->getBlog('description') ?></p>
            <img class="img-fluid" src="<?= $api->getFirstNameUrl('blog_logos') ?>" alt="<?= $api->getBlog('title') ?>" />
        </div>
    </footer>
    <!-- End Footer -->

    <div id="preloader"></div>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="node_modules/@srexi/purecounterjs/dist/purecounter_vanilla.js"></script>
    <script src="node_modules/aos/dist/aos.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="node_modules/glightbox/dist/js/glightbox.min.js"></script>
    <script src="node_modules/isotope-layout/dist/isotope.pkgd.min.js"></script>
    <script src="node_modules/swiper/swiper-bundle.min.js"></script>
    <script src="node_modules/typed.js/dist/typed.umd.js"></script>
    <script src="node_modules/waypoints/lib/noframework.waypoints.js"></script>
    <!-- Template Main JS File -->
    <script src="assets/MyResume/js/main.js"></script>
</body>

</html>