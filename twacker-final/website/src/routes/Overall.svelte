<script>
    import ChartPage from "../ChartPage.svelte";
    import Header from "../Header.svelte";
</script>
<body>
    <Header />
	<!-- <span id="scrollto"></span> -->
    <main>
		<h1>Overall Stats:</h1>
    
		<p></p>
        <ChartPage amount={12} interval={2592000} timelineLabels={new Array(12).fill("").map((_, i) => i == 11 ? "now" : `${12 -i}m`)}/>

    </main>
    <footer>
      <div id="left-column">
        <p>Website by Akash Iyer</p>
        <p><a href="https://www.instagram.com/wizardkashy/">Insta</a>, <a href="https://github.com/seventown/twacker">Github</a>, Discord: wiz#8479</p>
      </div>
      <div id="right-column">
        <p>Other projects<br>
        <a href="https://github.com/seventown/photomosaic" target="_blank">photomosaic</a> <br>
        </p>
      </div>
    </footer>
    <script>
      const hamburger = document.querySelector('.hamburger');
      const navLinks = document.querySelector('.navlinks');
      const links = document.querySelectorAll('.navlinks li');
      hamburger.addEventListener('click', () => {
        navLinks.classList.toggle("open");
      })
    </script>
  </body>
