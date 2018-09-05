/**
 * Generates the sliders used volume and time stamp position.
 *
 * @param  string  id      The ID of the element to be turned into a slider
 * @param  object  onDrag  Callback function fired once slider has been created
 */
function Slider(id, onDrag) {

	var range = document.getElementById(id),
		dragger = range.children[0],
		draggerWidth = 12, // width of your dragger
		down = false,
		rangeWidth, rangeLeft;

	dragger.style.width = draggerWidth + 'px';
	dragger.style.left = -draggerWidth + 'px';
	dragger.style.marginLeft = (draggerWidth / 2) + 'px';

	range.addEventListener("mousedown", function(e) {
		rangeWidth = this.offsetWidth;
		rangeLeft = this.offsetLeft;
		down = true;
		updateDragger(e);
		return false;
	});

	document.addEventListener("mousemove", function(e) {
		updateDragger(e);
	});

	document.addEventListener("mouseup", function() {
		down = false;
	});

	function updateDragger(e) {
		if (down && e.pageX >= rangeLeft && e.pageX <= (rangeLeft + rangeWidth)) {
			dragger.style.left = e.pageX - rangeLeft - draggerWidth + 'px';
			if (typeof onDrag == "function") onDrag(Math.round(((e.pageX - rangeLeft) / rangeWidth) * 100));
		}
	}

}
