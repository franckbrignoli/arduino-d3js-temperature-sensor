<html>
<head>	
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<style>
body {
  font: 10px sans-serif;
}

.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.line {
  fill: none;
  stroke: steelblue;
  stroke-width: 2.5px;
}

circle {
	fill: #315B7E;
	stroke: black;
	stroke-width: 1.5px;
}

circle:hover {
	fill: #F5DA42;
	stroke: black;
	stroke-width: 0.5px;
}

#avg {
  fill: red;
  stroke: red;
  stroke-width: 0.5px;
}

#avg_text {
  fill: red;
}

#info {
 	position: absolute;
	text-align: center;
	background-color: #FFF;
	border: 1px solid #315B7E;
	padding: 4px;
	border-radius: 4px;
	pointer-events: none;
	display: none;
}

</style>
</head>
<body>
	<div id="info"></div>
	<script type="text/javascript" src="http://d3js.org/d3.v3.min.js"></script>
	<script type="text/javascript">
	<?php

	require('TemperatureTable.php');	

	echo "var dataset = [";
	$rows = TemperatureTable::getTodayReport(15);

	foreach ($rows as $row) 
	{
		echo "{ date : '".$row['reported_at']."', temperature : '".$row['temperature']."' },";
	}
	echo "];";

	foreach (TemperatureTable::getTodayAverage() as $row) 
	{
		echo "var avg = ".(isset($row['temperature']) ? $row['temperature'] : 0).";";
	}

	?>	

	function displayInfo(obj, d)
	{
		coord = d3.mouse(obj)

		d3.select('#info')
			.style('display', 'block')
			.style('left', (coord[0] + margin.left) + 'px')
			.style('top', (coord[1] + margin.top - 20) + 'px')
			.html(d.date + ' '+(Math.round(d.temperature*10) / 10)+'°C');
	}

	function hideInfo()
	{
		d3.select('#info')
			.style('display', 'none');
	}

	var margin = { top: 40, right: 40, bottom: 40, left: 40 };
	var width = 960 - margin.left - margin.right,
		height = 400 - margin.top - margin.bottom;

	var parse_time = d3.time.format.utc("%H:%M:%S").parse;

	var x = d3.time.scale()
				.range([0, width])
				.domain([parse_time("00:00:00"), parse_time("23:59:59")]);

	var y = d3.scale.linear()
				.range([height, 0])
				.domain([15, 30]);


	var xAxis = d3.svg.axis()
		.scale(x)
		.orient("bottom");

	var yAxis = d3.svg.axis()
		.scale(y)
		.orient("left");
	
	var svg = d3.select('body')
				.append('svg')
				.attr('width', width + margin.right + margin.left)
				.attr('height', height + margin.top + margin.bottom)
				.append('g')
				.attr('transform', "translate("+margin.left+", "+margin.top+")");

	svg.append("g")
		.attr("class", "y axis")
		.call(yAxis);

	svg.append("g")
	  .attr("class", "x axis")
	  .attr("transform", "translate(0," + height + ")")
	  .call(xAxis);

	var line = d3.svg.line()
				.interpolate('basis')
				.x(function(d) { return x(parse_time(d.date)); })
				.y(function(d) { return y(d.temperature); });
	
	svg.append('line')
		.attr('id', 'avg')
		.attr('x1', 0)
		.attr('y1', y(avg))
		.attr('x2', width)
		.attr('y2', y(avg));

	svg.append("path")
		.datum(dataset)
		.attr("class", "line")
		.attr("d", line);

	svg.selectAll('circle')
		.data(dataset)
		.enter()
		.append('circle')
		.attr('cx', function(d) { return x(parse_time(d.date)); })
		.attr('cy', function(d) { return y(d.temperature); })
		.attr('r', '3')
		.on('mouseover', function(d) {
			displayInfo(this, d);	
		})
		.on('mouseout', function(d) { 
			hideInfo();
		});

	svg.append("text")
	  .attr('id', "avg_text")
      .attr("x", width + 10)
      .attr("y", y(avg))
      .attr("dy", ".35em")
      .text((Math.round(avg*10)/10)+"°C");
	</script>
</body>
</html>

