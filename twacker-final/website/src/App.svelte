<script>
    import ChartPage from "./ChartPage.svelte";
    import Header from "./Header.svelte";
    import { Router, Route } from "svelte-routing";
    import Week from "./routes/Week.svelte"
    import Month from "./routes/Month.svelte"
    import Overall from "./routes/Overall.svelte"
    import Today from "./routes/Today.svelte"
    export let url  = "";
</script>
<body>
  <Router url="{url}">
    <Route path="today" component={Today} />
    <Route path="week" component={Week} />
    <Route path="month" component={Month} />
    <Route path="overall" component={Overall} />
    <Route path="/" component={Today} />
  </Router>
</body>
  
