<!-- wish-22 -->
<?php
    require('secure_conn.php');
    require('includes/header.php');
?>
<aside class="indexintro">
    <p>Upload 1min long songs.</p>
    <p>Receive VOTES to gain points.</p>
    <p>Obtain a big score.</p>
    <p>Have fun!</p>
    <p>And good luck.</p>
    <br>
    <p class="screenonly" style="font-size:60%">this is our friend Apple, click on her for a noise</p>
    <p class="printonly" style="font-size:60%">this is our friend Apple, she appreciates you dearly</p>
    <img src="images/apple.png" alt="Small drawing of a cat named Apple.">
</aside>

<script>
    // Hook Apple cat up to an event listener to play a small noise

    var applecat = document.querySelector("aside img");
    var audio = new Audio("fried.mp3");
    
    console.log("hi my name is apple and i am in the console");
    console.log("i am very happy :D");

    applecat.addEventListener('click', function () {
        audio.pause();
        audio.currentTime = 0;
        audio.play();
    })
</script>

<main class="screenonly" style="text-align: center;">
    <button onclick='print()'>Print</button>
</main>
<?php require('includes/footer.php'); ?>