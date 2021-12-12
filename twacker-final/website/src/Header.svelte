<script>
    import ChartPage from "./ChartPage.svelte";
    import Header from "./Header.svelte";
    import { Router, Link, Route } from "svelte-routing";
    import Week from "./routes/Week.svelte"
    import Month from "./routes/Month.svelte"
    import Overall from "./routes/Overall.svelte"

    export let url  = "";
</script>
<header>
  
  <nav id="navbar">
    <!-- <img src="images/site-elements/seventownfavicon.png" alt="home logo"> -->
    <ul class="navlinks">
      <li><Link to="today">Today</Link></li>
      <li><Link to="week">This week</Link></li>
      <li><Link to="month">This month</Link> </li>
      <li><Link to="overall">All Time</Link> </li>
    </ul>
    <div class="hamburger">
      <div class="line"></div>
      <div class="line"></div>
      <div class="line"></div>
    </div>
  </nav>
  <!-- <div id="header-no-nav">
    <img src="/images/twacker.png" alt="header logo" class="hlogo" id="headerdesktop">
    <img src="/images/twacker.png" alt="header logo" class="hlogo" id="headermobile">
  </div> -->
</header>
  