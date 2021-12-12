<script>
    import ChartPage from "../ChartPage.svelte";
    import Header from "../Header.svelte";
</script>
<body>
    <Header />
	<!-- <span id="scrollto"></span> -->
    <main>
		<h1>Today's Stats:</h1>
        <ChartPage amount={24} interval={3600} timelineLabels={["23h", "22h", "21h", "20h", "19h", "18h", "17h", "16h", "15h", "14h", "13h", "12h", "11h", "10h", "9h", "8", "7h", "6h", "5h", "4h", "3h", "2h", "1h", "now"]}/>

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
