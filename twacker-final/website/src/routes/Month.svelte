<script>
    import { _ } from "chart.js/dist/chunks/helpers.segment"; // what is this, wtf

    import ChartPage from "../ChartPage.svelte";
    import Header from "../Header.svelte";
</script>
<body>
    <Header />
    <main>
		<h1>This Month's Stats:</h1>
    
		<p></p>
        <ChartPage amount={30} interval={86400} timelineLabels={new Array(30).fill(0).map((_, i) => i == 29 ? "now" : `${29 -i}d`)}/>

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
