<?php
	require(dirname(__FILE__)."/../../../../wp-blog-header.php");

	// check to see if the user has enabled gzip compression in the WordPress admin panel
	if ( !get_settings('gzipcompression') and !ini_get('zlib.output_compression') and ini_get('output_handler') != 'ob_gzhandler' and ini_get('output_handler') != 'mb_output_handler' ) {
		ob_start('ob_gzhandler');
	}

	// The headers below tell the browser to cache the file and also tell the browser it is JavaScript.
	header("Cache-Control: public");
	header("Pragma: cache");

	$offset = 60*60*24*60;
	$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s",time() + $offset)." GMT";
	$LmStr = "Last-Modified: ".gmdate("D, d M Y H:i:s",filemtime(__FILE__))." GMT";

	header($ExpStr);
	header($LmStr);
	header('Content-Type: text/javascript; charset: UTF-8');
?>

/**
 * SBM plugin's main JS file
 **/

/* Classes ****************************************************************************************/

/**
 * The main class for managing SBMs
 **/
var sbmManager = {
	dndBox: null,
	optionsBox: null,
	addForm: null,
	optionsForm: null,

	initialize: function() {
		// Setup the DnD for the sidebar lists
		sbmManager.dndBox = new sbmDnDBox("sbm-dnd", "tab-module-options");

		// Setup the tabs
		sbmManager.optionsBox = new sbmTabBox("sbm-options");

		// Setup the add form
		sbmManager.addForm = new sbmAddForm("module-add", "module-add-error");

		// Setup the options form
		sbmManager.optionsForm = new sbmOptionsForm("module-options-form", "module-options-desc");
	}
}

/**
 * The class for module DnD
 **/
var sbmDnDBox = Class.create();
sbmDnDBox.prototype = {
	initialize: function(id, selectTab) {
		// Get the Dnd box
		this.dndBox = $(id);

		// The tab to select
		this.selectTab = selectTab;

		// Get the lists
		this.lists = this.dndBox.getElementsByTagName("ul");

		// Get the list ids
		this.listIds = [];
		for(var i = 0; i < this.lists.length; i++) {
			this.listIds[i] = this.lists[i].id;
		}

		// The last order data
		this.lastOrderData = "";

		this.update();
	},

	reorder: function() {
		var orderData = "";

		for(var i = 0; i < this.lists.length; i++) {
			var list = this.lists[i];

			if(list.hasChildNodes()) {
				for(var j = 0; j < list.childNodes.length; j++) {
					orderData += "sidebar_ordering[" + list.id + "][" + j + "]=" + list.childNodes[j].id.match(/^sbm\_(.+)$/)[1];

					if(j < list.childNodes.length - 1) {
						orderData += "&";
					}
				}
			}

			if(i < this.lists.length - 1) {
				orderData += "&";
			}
		}

		// Only update the order if it's different
		if(orderData != this.lastOrderData) {
			// Check if the currently selected module, if any, has changed position
			if(sbmManager.optionsForm.moduleId != null) {
				sbmManager.optionsForm.sidebarId = $("sbm_" + sbmManager.optionsForm.moduleId).parentNode.id;
			}

			new Ajax.Request(sbm_baseUrl + "?action=reorder", {
					postBody: orderData
				});

			this.lastOrderData = orderData;
		}
	},

	update: function() {
		new Ajax.Request(sbm_baseUrl + "?action=list", {
				onComplete: this.updateComplete.bindAsEventListener(this)
			});
	},

	updateComplete: function(response) {
		var moduleLists = response.responseXML.getElementsByTagName("modules");

		// Setup the DnD for the lists
		for(var i = 0; i < this.lists.length; i++) {
			var list = this.lists[i];

			// Delete the old nodes
			while(list.hasChildNodes()) {
				list.removeChild(list.firstChild);
			}

			var moduleList = moduleLists[i];

			// Create module elements and add them to the lists
			if(moduleList.hasChildNodes()) {
				for(var j = 0; j < moduleList.childNodes.length; j++) {
					var module = moduleList.childNodes[j];
					var listElement = document.createElement("li");
					var listLinkElement = document.createElement("a");

					listElement.id = "sbm_" + module.getAttribute("id");

					listLinkElement.href = "#";
					listLinkElement.onclick = this.listElementClick.bindAsEventListener(this);

					listLinkElement.appendChild(document.createTextNode(module.firstChild.nodeValue));
					listElement.appendChild(listLinkElement);

					list.appendChild(listElement);
				}
			}

			// Make the lists sortable
			Sortable.create(list, {
					dropOnEmpty: true,
					constraint: false,
					containment: this.listIds,
					onUpdate: this.reorder.bindAsEventListener(this)
				});
		}
	},

	listElementClick: function(evt) {
		var moduleListElement = Event.element(evt || window.event).parentNode;
		var moduleId = moduleListElement.id.match(/^sbm\_(.+)$/)[1];

		sbmManager.optionsForm.showDesc();

		// Get IDs
		sbmManager.optionsForm.sidebarId = moduleListElement.parentNode.id;
		sbmManager.optionsForm.moduleId = moduleId;

		new Ajax.Updater("module-options-custom", sbm_baseUrl + "?action=control-show", {
			postBody: "module_id=" + moduleId,
			onComplete: this.customOptionsComplete.bindAsEventListener(this)
		});

		// Stop page reload
		return false;
	},

	customOptionsComplete: function(evt) {
		// Get the success and error elements
		sbmManager.optionsForm.successElement = $("module-update-success");
		sbmManager.optionsForm.errorElement = $("module-update-error");

		// Setup content loading URLs and options
		var toggleDiv = $("specific-posts");
		toggleDiv.contentURL = sbm_baseUrl + "?action=control-post-list-show";
		toggleDiv.contentURLPostBody = "module_id=" + sbmManager.optionsForm.moduleId;

		toggleDiv = $("specific-pages");
		toggleDiv.contentURL = sbm_baseUrl + "?action=control-page-list-show";
		toggleDiv.contentURLPostBody = "module_id=" + sbmManager.optionsForm.moduleId;

		// Scan for toggle links
		sbmSpecialLinks.scan(sbmManager.optionsForm.optionsForm);
		
		sbmManager.optionsForm.showForm();

		sbmManager.optionsBox.tabs[this.selectTab].show();
	}
}

/**
 * Helper class for links
 **/
var sbmSpecialLinks = {
	scan: function(id) {
		var links = $(id).getElementsByTagName("a");

		if(links) {
			for(var i = 0; i < links.length; i++) {
				var id = links[i].id;
				var matchId = null;

				if((matchId = id.match(/^toggle\-(.+)$/))) {
					new sbmToggleLink(id, matchId[1]);
				} else if(matchId = id.match(/^check\-(.+)$/)) {
					new sbmCheckLink(id, matchId[1]);
				} else if(matchId = id.match(/^uncheck\-(.+)$/)) {
					new sbmUncheckLink(id, matchId[1]);
				}
			}
		}
	}
}

/**
 * The class for toggle links
 **/
var sbmToggleLink = Class.create();
sbmToggleLink.prototype = {
	initialize: function(id, attachId) {
		this.toggleLink = $(id);
		this.attachElement = $(attachId);
		this.toggleText = this.toggleLink.innerHTML;

		this.toggleLink.innerHTML = this.toggleText + " &rarr;";
		this.toggleLink.onclick = this.onToggleClick.bindAsEventListener(this);

		this.toggled = false;
	},

	onToggleClick: function(evt) {
		if(this.toggled) {
			this.toggleLink.innerHTML = this.toggleText + " &rarr;";
			this.attachElement.style.display = "none";
		} else {
			this.toggleLink.innerHTML = this.toggleText + " &darr;";
			this.attachElement.style.display = "block";
		}

		this.toggled = !this.toggled;

		if(this.attachElement.contentURL && !this.attachElement.contentLoaded) {
			this.attachElement.innerHTML = "Loading...";

			new Ajax.Updater(this.attachElement, this.attachElement.contentURL, {
				postBody: this.attachElement.contentURLPostBody ? this.attachElement.contentURLPostBody : "",
				onComplete: this.onAttachElementContentComplete.bindAsEventListener(this)
			});
		}

		return false;
	},

	onAttachElementContentComplete: function(evt) {
		this.attachElement.contentLoaded = true;

		// Scan for special links
		sbmSpecialLinks.scan(this.attachElement);
	}
}

/**
 * The class for check links
 **/
var sbmCheckLink = Class.create();
sbmCheckLink.prototype = {
	initialize: function(id, checkBoxListId) {
		$(id).onclick = this.linkClick.bindAsEventListener(this);
		this.checkBoxes = $(checkBoxListId).getElementsByTagName("input");
	},

	linkClick: function(evt) {
		for(var i = 0; i < this.checkBoxes.length; i++) {
			this.checkBoxes[i].checked = true;
		}

		return false;
	}
}

/**
 * The class for uncheck links
 **/
var sbmUncheckLink = Class.create();
sbmUncheckLink.prototype = {
	initialize: function(id, checkBoxListId) {
		$(id).onclick = this.linkClick.bindAsEventListener(this);
		this.checkBoxes = $(checkBoxListId).getElementsByTagName("input");
	},

	linkClick: function(evt) {
		for(var i = 0; i < this.checkBoxes.length; i++) {
			this.checkBoxes[i].checked = false;
		}

		return false;
	}
}

/**
 * The class for tab boxes
 **/
var sbmTabBox = Class.create();
sbmTabBox.prototype = {
	initialize: function(id) {
		// Get the box
		this.tabBox = $(id);

		// Get the tab bar
		this.tabBar = this.tabBox.getElementsByTagName("ul")[0];

		// Get the tab bar tabs
		var tabs = this.tabBar.getElementsByTagName("li");
		this.tabs = {};
		var first = null;

		// Setup the tabs
		for(var i = 0; i < tabs.length; i++) {
			var id = tabs[i].id.match(/^show\-(.+)$/)[1];

			this.tabs[id] = new sbmTab(this, tabs[i], $(id));

			if(i == 0) {
				first = this.tabs[id];
			}
		}

		// Show the first tab
		first.show();
	}
}

/**
 * The class to describe a tab of a tab box
 **/
var sbmTab = Class.create();
sbmTab.prototype = {
	initialize: function(tabBox, tab, control) {
		this.tabBox = tabBox;
		this.tab = tab;
		this.control = control;

		// Setup the clicking event
		this.tab.getElementsByTagName("a")[0].onclick = this.show.bindAsEventListener(this);
	},

	show: function() {
		this.tab.className = "selected";
		this.control.style.display = "block";

		if(this.tabBox.oldTab != null && this.tabBox.oldTab != this)
			this.tabBox.oldTab.hide();

		this.tabBox.oldTab = this;

		// Stop page reload
		return false;
	},

	hide: function() {
		this.tab.className = "";
		this.control.style.display = "none";
	}
}

/**
 * The class for the add form
 **/
var sbmAddForm = Class.create();
sbmAddForm.prototype = {
	initialize: function(id, errorId) {
		this.addForm = $(id);
		this.errorElement = $(errorId);

		// Setup the submit event
		this.addForm.onsubmit = this.add.bindAsEventListener(this);
	},

	add: function() {
		this.errorElement.style.display = "none";

		// Disable the form
		Form.disable(this.addForm);

		// Serialize the data
		var formData = Form.serialize(this.addForm);

		// Send the information via AJAX
		new Ajax.Request(sbm_baseUrl + "?action=add", {
				postBody: formData,
				onComplete: this.addComplete.bindAsEventListener(this)
			});

		// Stop page reload
		return false;
	},

	addComplete: function(response) {
		if(sbmResponse.checkValid(response, this.addError.bindAsEventListener(this))) {
			// Reset the form's data
			Form.reset(this.addForm);

			// Enable the form
			Form.enable(this.addForm);

			// Update the DnD box
			sbmManager.dndBox.update();
		} else {
			// Enable the form
			Form.enable(this.addForm);

			// Select the form
			Form.findFirstElement(this.addForm).focus();
		}
	},

	addError: function(error) {
		this.errorElement.style.display = "block";

		this.errorElement.innerHTML = error;
	}
}

/**
 * The class for the options form
 **/
var sbmOptionsForm = Class.create();
sbmOptionsForm.prototype = {
	sidebarId: null,
	moduleId: null,
	successElement: null,
	errorElement: null,

	initialize: function(id, descId) {
		var self = this;

		this.optionsForm = $(id);
		this.optionsDesc = $(descId);

		var buttons = this.optionsForm.getElementsByTagName("input");

		// Setup remove buttons
		for(var i = 0; i < buttons.length; i++) {
			if(buttons[i].type == "button" && buttons[i].className.indexOf("remove") != -1) {
				buttons[i].onclick = function(e) {
					self.optionsForm.onremove();

					return false;
				}
			}
		}

		// Setup the form events
		this.optionsForm.onsubmit = this.update.bindAsEventListener(this);
		this.optionsForm.onremove = this.remove.bindAsEventListener(this);

		this.showDesc();
	},

	showForm: function() {
		this.optionsForm.style.display = "block";
		this.optionsDesc.style.display = "none";
	},

	showDesc: function() {
		this.optionsForm.style.display = "none";
		this.optionsDesc.style.display = "block";
	},

	update: function() {
		// Hide the success and error elements
		this.successElement.style.display = "none";
		this.errorElement.style.display = "none";

		// Disable the form
		Form.disable(this.optionsForm);

		// Serialize the data
		var formData = "sidebar_id=" + this.sidebarId + "&module_id=" + this.moduleId + "&" + Form.serialize(this.optionsForm);

		// Send the information via AJAX
		new Ajax.Request(sbm_baseUrl + "?action=update", {
				postBody: formData,
				onComplete: this.updateComplete.bindAsEventListener(this)
			});

		// Stop page reload
		return false;
	},

	updateComplete: function(response) {
		// Enable the form
		Form.enable(this.optionsForm);

		// Check the response was valid
		if(sbmResponse.checkValid(response, this.updateError.bindAsEventListener(this))) {
			// Show the success element
			this.successElement.style.display = "block";

			// Update the DnD view (change of module names)
			sbmManager.dndBox.update();
		}
	},

	updateError: function(error) {
		this.errorElement.style.display = "block";

		this.errorElement.innerHTML = error;
	},

	remove: function() {
		// Disable the form
		Form.disable(this.optionsForm);

		var removeData = "sidebar_id=" + this.sidebarId + "&module_id=" + this.moduleId;

		// Send the delete information via AJAX
		new Ajax.Request(sbm_baseUrl + "?action=remove", {
				postBody: removeData,
				onComplete: this.removeComplete.bindAsEventListener(this)
			});
	},

	removeComplete: function(response) {
		// Enable the form
		Form.enable(this.optionsForm);

		this.showDesc();

		// Update the DnD view
		sbmManager.dndBox.update();
	}
}


/**
 * Helper class for responses
 */
var sbmResponse = {
	checkValid: function(response, errorCallback) {
		// Check for an <error> element
		var errorElement = response.responseXML.getElementsByTagName("error")[0];

		if(errorElement) {
			var error = errorElement.firstChild.nodeValue;

			if(errorCallback) {
				errorCallback(error);
			} else {
				// Show the error
				alert("Error: " + error);
			}

			return false;
		} else {
			return true;
		}
	}
}


/* Bootstrap **************************************************************************************/

Event.observe(window, "load", sbmManager.initialize);
