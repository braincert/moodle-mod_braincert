 /* eslint-env jquery*/

/**
 * When the user clicks on the button, toggle between hiding and showing the dropdown content.
 * @param {id} id The dropdown number.
 * @return {boolean} false.
 */
function dropdownmenu(id) { // eslint-disable-line no-unused-vars
    jQuery(".class_list").find(".drop_fr_icon").find(".dropdown-content"
	).each(function(index) { // eslint-disable-line no-unused-vars
        if (jQuery(this).attr("id") == "dropdown-" + id) {
            if (jQuery(this).hasClass("show")) {
                jQuery(this).removeClass("show");
            } else {
                jQuery(this).addClass("show");
            }
        } else {
            if (jQuery(this).hasClass("show")) {
                jQuery(this).removeClass("show");
            }
        }
    });
}

/**
 * Close the dropdown if the user clicks outside of it.
 * @param {event} event click.
 * @return {boolean} false.
 */
window.onclick = function(event) {
    if (!event.target.matches('.dropbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
};
