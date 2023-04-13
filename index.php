<?php

class Api
{
    public $blogName;
    protected $data;

    public function __construct($blogName)
    {
        $this->blogName = $blogName;
    }

    public function getApiBaseUrl()
    {
        return 'http://localhost/akrez5/api';
        return 'https://akrez.ir/api';
    }

    public function getGalleryUrl($name)
    {
        return 'https://akrezing.ir/gallery/' . $name;
    }

    public function callApi($url)
    {
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
        $response = curl_exec($curl);
        curl_close($curl);

        return (array)json_decode($response, true);
    }

    public function getData()
    {
        if (null !== $this->data) {
            return $this->data;
        }

        $url = $this->getApiBaseUrl() . '/' . $this->blogName;
        return $this->data = $this->callApi($url);
    }

    public function getValues($index)
    {
        $data = $this->getData();

        $result = [];
        foreach ($data[$index] as $item) {
            $result = array_merge($result, $item['values']);
        }
        return $result;
    }

    public function getGalleries($index)
    {
        $data = $this->getData();

        $result = [];
        foreach ($data[$index] as $item) {
            $result = array_merge($result, $item['names']);
        }
        return $result;
    }

    public function getFirstGallery($index)
    {
        $names = $this->getGalleries($index);
        if ($names) {
            return reset($names);
        }
        return null;
    }

    public function getFirstGalleryUrl($index)
    {
        $name = $this->getFirstGallery($index);
        if ($name) {
            return $this->getGalleryUrl($name);
        }
        return null;
    }

    public function getBlog($index)
    {
        $data = $this->getData();
        return $data['blog'][$index];
    }
}

$api = new Api('shahabtahrir');

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
    <link href="<?= $api->getFirstGalleryUrl('blog_logos') ?>" rel="icon" />
    <link href="<?= $api->getFirstGalleryUrl('blog_logos') ?>" rel="apple-touch-icon" />

    <style>
        #hero {
            background: url("<?= $api->getFirstGalleryUrl('blog_heros') ?>") top right no-repeat;
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