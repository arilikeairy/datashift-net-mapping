<?php 
/**
 * Template Name: d3 MAP: Pinned focus areas ONLY
 **/
get_header(); 
  if( get_post_meta(get_the_ID(), 'header', true) != 'no') echo avia_title();
  do_action( 'ava_after_main_title' ); 
?>

<div class="container"><div class="template-page"><div class="post-entry"><div class="entry-content-wrapper clearfix main_color"><div class="tabcomtainer">

<!-- get just teaser content -->
<div id="intro">
<?php
// Fetch post content
$content = get_post_field( 'post_content', get_the_ID() );
// Get content parts
$content_parts = get_extended( $content );
// Output part before <!--more--> tag
echo $content_parts['main'];
?>
</div>

<div class="legend">
	<div class="key">
		<h3>Key</h3>
		<ul class="key">
			<li class="initiative"><span class="initiative">Blue nodes are citizen generated data initiatives</span></li>
			<li class="sdg"><span class="sdg">Orange nodes are UN Sustainable Development Goals</span></li>
			<li class="focusarea"><span class="focusarea">Green nodes are Continents/Global</span></li>
		</ul>
		Double click a node to unpin it.
		<br><button onclick="force.stop()">Pause Auto-Layout</button>
	</div>
	<div class="infobox"></div>
</div>
<div id="svg"></div>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script type='text/javascript' src="http://labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"> </script>
<script>

var width = 960,
    height = 500;

var color = d3.scale.category10();

var force = d3.layout.force()
    .charge(-120)
    .linkDistance(40)
    .size([width, height]);
	
var drag = force.drag()
    .on("dragstart", dragstart);
	
var svg = d3.select("#svg").append("svg")
    .attr("width", width)
    .attr("height", height);
	
var tip = d3.tip()
    .attr('class', 'd3-tip')
    .offset([-10, 0])
    .html(function (d) {
    return  d.name + "</span>";
})
svg.call(tip);
  var link = svg.selectAll(".link");
  var node = svg.selectAll(".node");
  
d3.json('http://datashift.zardtech.com/learning-zone/visualizations/json-focus-areas/', function(error, graph) {
  if (error) throw error;

  force
      .nodes(graph.nodes)
      .links(graph.links)
      .start();

  link = link.data(graph.links)
    .enter().append("line")
      .attr("class", "link");

  node = node.data(graph.nodes)
    .enter().append("g")
      .attr("class", "node nodegroup" )
      .call(drag);
	  
   node.append("circle")
		.attr("r", 10)
		.attr("x", -8)
		.attr("y", -8)
		.attr("width", 16)
		.attr("height", 16)
		.attr("class", function(d) { return d.type; })
		.on("dblclick", dblclick)
		//.style("fill", function(d) { return color(d.group); })
		.on('mouseover', tip.show)
		.on('mouseout', tip.hide);

  node.append("title")
      .text(function(d) { return d.name; });
	  
  node.append("text")
      .attr("dx", 12)
      .attr("dy", ".35em")
	  .attr("class",function(d) { return d.type; })
      .text(function(d) { return d.label });
	  
  d3.select("#plot")
        .append("g")
        .classed("infobox",1);
  
        d3.select(".infobox")
        .append("rect")
        .attr("x", 10)
        .attr("y", 5)
        .attr("rx", 5)
        .attr("ry", 5)
        .attr("height", 52)
        .attr("width", 205);

        d3.select(".infobox") 
        //.append("a")
	.append("text")
        .text("Click any circle to see more information.")
        .attr("x", 15)
        .attr("y", 36)
        ;  
		
	// Add click event
    d3.selectAll(".node,.link")
      .on("click", function(d,i) {
        var d = this.__data__;
        text = "Name: " + d.name + "\n Description: " + d.descr + "\n Read more about this initiative.";
        d3.selectAll(".infobox")
        .selectAll("text").text(text)
        .html('<span>Name:</span> ' + d.name + '<br><span>Description:</span> ' + d.descr + '<br><a href= "' + d.url + '">Read more about this initiative.</a>');
      });
	      
  force.on("tick", function() {
    link.attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });

    node.attr("transform", function(d) { 
		return "translate(" + d.x + "," + d.y + ")"; 
	});
  });
});
function dblclick(d) {
  d3.select(this).classed("fixed", d.fixed = false);
}
function dragstart(d) {
  d3.select(this).classed("fixed", d.fixed = true);
}

</script>

<div id="outro">
<?php // Output part after <!--more--> tag
echo $content_parts['extended']; ?>
</div>
</div></div></div></div></div>
<?php get_footer(); ?>