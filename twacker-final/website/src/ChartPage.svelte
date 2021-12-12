<script>
	import "./styles.css";
  import Line from "svelte-chartjs/src/Line.svelte"
  import Pie from "svelte-chartjs/src/Pie.svelte"
  import Bar from "svelte-chartjs/src/Bar.svelte"
  export let interval;
  export let timelineLabels;
  export let amount;

import { I, _ } from "chart.js/dist/chunks/helpers.segment";
	async function fetchStats() {
		let json = await (
			await fetch("./data.json")
		).json();
        const d = new Date().valueOf() / 1000;
        if (amount != 12) {
          json.data = json.data.filter(u => u.startTime > (d - amount * interval));
        }
		return json;
	}
    
    async function fetchAggregatedStats() {
      let json = await fetchStats();
      // console.log(json)
      return aggregate(json);
    }
    function aggregate(json) {
      // console.log(json)
      let aggregate = {"data": []}
      let setNames = new Set(json.data.map(u => u.appName))
      setNames.forEach(function(appName) {
        let SameName = json.data.filter(u => u.appName == appName)
        // console.log(SameName)
        let totalTime = SameName.map(u => (u.endTime - u.startTime) / 60).reduce((a, b) => a + b)
        // console.log(appName)
        // console.log(totalTime)
        aggregate.data.push({"appName": appName, "totalTime": totalTime})
      })
      return aggregate;
    }
    function convertToCharts(json) {
      // console.log(json)
      // console.log(Math.floor(Math.random() * 256))
      let colors = new Array(json.data.length).fill(``);
      
      colors.forEach((_, i, a) => colors[i] = `rgba(${Math.floor(255 - Math.random() * 100)}, ${Math.floor(255 - Math.random() * 100)}, ${Math.floor(255 - Math.random() * 100)}, 0.5)`)
      let borders = new Array(json.data.length).fill(``);
      borders.forEach((_, i, a) => borders[i] = colors[i].substring(0, colors[i].length - 4) + "1.0)")
      // console.log(colors)
      let aggregate = {
        labels: json.data.map(u => u.appName),
        datasets: [
          {
            label: "Time Spent (minutes)",
            data: json.data.map(u => u.totalTime),
            backgroundColor: colors,
            borderColor: borders
          }
        ]
      }
      
      return aggregate;
    }
    async function fetchTimelineStats() {
      let json = await fetchStats();
      // console.log(json)
      return timeline(json);
    }
    function timeline(json) {
      // initialize the datasets dictionary with the set of all apps
      let d = new Date().valueOf() / 1000;
      console.log(d);
      let datasets = {"data": {}}
      
      let weekData = json.data.filter(u => u.startTime >= d - amount * interval && u.startTime <=d)
      console.log(weekData); debugger;
      let setOfNames = new Set(weekData.map(u => u.appName))
      // console.log(setOfNames);
      setOfNames.forEach(function(appName) {
        datasets.data[appName] = new Array(amount).fill(0)
        
      })
      for (let i = 0; i < amount; i++) {
        let data = json.data.filter(u => u.startTime >= d - interval && u.startTime <=d );
        // console.log(data)
        d -= interval;
        data.forEach(function(entry) {
          datasets.data[entry.appName][amount - 1 - i] += (entry.endTime - entry.startTime) / 60;

        })
      }
      console.log(datasets);
      return getLine(datasets);
    }
    function getLine(json) {
          let dataLine = {
              labels: timelineLabels,
              datasets: []
          }
          Object.keys(json.data).forEach(function(key) {
              let mainColor = `rgba(${Math.floor(255 - Math.random() * 100)}, ${Math.floor(255 - Math.random() * 100)}, ${Math.floor(255 - Math.random() * 100)}`;
              // console.log(json.data[key]);
              dataLine.datasets.push({
                  label: key,
                  fill: true,
                  lineTension: 0.3,
                  backgroundColor: mainColor + ", .3)",
                  borderColor: mainColor + ", 158)",
                  borderCapStyle: "butt",
                  borderDash: [],
                  borderDashOffset: 0.0,
                  borderJoinStyle: "miter",
                  pointBorderColor: "rgb(205, 130,158)",
                  pointBackgroundColor: "rgb(255, 255, 255)",
                  pointBorderWidth: 10,
                  pointHoverRadius: 5,
                  pointHoverBackgroundColor: "rgb(0, 0, 0)",
                  pointHoverBorderColor: "rgba(220, 220, 220,1)",
                  pointHoverBorderWidth: 2,
                  pointRadius: 1,
                  pointHitRadius: 10,
                  data: json.data[key]
              })
          })
          console.log(dataLine);
          return dataLine;
      }
</script>
    <div class="container">
      {#await fetchAggregatedStats()}
      {:then json}
      {#await convertToCharts(json)}
      Loading...
      {:then data}
      {@debug data}
      <div class="subchart"><Pie data={data} width={100} height={100} options={{ maintainAspectRatio: false}} /></div>
      <div class="subchart"><Bar data={data} width={100} height={50} /></div>
      {/await}
      {/await}</div>

      {#await fetchTimelineStats()}
      {:then data}
      {@debug data}
      <Line data={data} width={100} height={50} />
      {/await}