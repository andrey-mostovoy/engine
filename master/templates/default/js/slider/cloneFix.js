/*
 * call this function on a scrollable node to add some more clones on the right side of the carousel
 * as the jQuery Tools Scrollable plugin is build to just show one item on screen at once (not
 * multiple small elements). This function fills up the empty space right to the carousel with dummy
 * nodes
 * @example 
 * $(selector).scrollable({circular:true});
 * $(selector).scrollableAddClones();
 * @param int addItems [OPTIONAL] define the number of clone items to aditionally add to the wrapper
 * @return void
 */
$.fn.scrollableAddClones = function(addItems) {
  // grab scrollable plugin
  var scrollable;
  if (!(scrollable = $(this).data('scrollable')) || !scrollable.getConf().circular)
    return;
  // grab scrollable elements and remember it's count
  var nodes = scrollable.getItems();
  var length = nodes.length;
  // grab class for the nodes
  var clonedClass = scrollable.getConf().clonedClass;
  // get wrap object to append the clones to
  var wrap = scrollable.getItemWrap();
  // fill as much nodes as needed for 500 pixels
  if (!addItems) addItems = Math.ceil(500 / nodes.eq(1).width());
  // create fake container to add the clones to (max 15 clones)
  var newNodesAppend = $('<span />');
  for (var i = 1; i <= (addItems < 15 ? addItems : 15); i++)
    nodes.eq(i % length).clone().addClass(clonedClass).appendTo(newNodesAppend);
  // insert HTML
  newNodesAppend.children().appendTo(wrap);
}