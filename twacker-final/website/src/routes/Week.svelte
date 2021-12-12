<script>
    import ChartPage from "../ChartPage.svelte";
    import Header from "../Header.svelte";
</script>
<body>
    <Header />
	<!-- <span id="scrollto"></span> -->
    <main>
		<h1>This Week's Stats:</h1>
    
      <ChartPage amount={7} interval={86400} timelineLabels={new Array(7).fill("").map((_, i) => i == 6 ? "now" : `${6 - i}d`)}/>

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
