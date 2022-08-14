<?php 
/*
* Template Name: Connexion
*/
// include('templates/head.php');

// get_headers()

get_header("header");

echo '<pre>';
print_r(get_template_directory_uri()."/parts/header.html");
echo '</pre>';

// include(get_template_directory() . '/header.php');



?>

<div class="test">
    <div class="div-test">
        <h1>Bonjour</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ullam eius recusandae officiis fuga voluptatum. Dolores ut numquam ad repellendus? Voluptatum similique voluptate aperiam porro consequatur maiores, soluta iusto numquam ipsam.</p>
    </div>
</div>

<?php
include(get_template_directory_uri() . '/footer.php');
?>