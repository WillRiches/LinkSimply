<h1>LinkSimp.ly</h1>

<div id="divActiveZone">
	<div class="form-inline">
		<div class="form-group">
			<input type="text" class="form-control" id="inputUrlSource" placeholder="Link to shorten">
		</div>
		<div class="form-group">
			<input type="text" class="form-control" id="inputUrlTarget" placeholder="Custom url (optional)">
		</div>
		<button id="buttonShorten" class="btn btn-default">Shorten</button>
	</div>
</div>
<p id="pNotify"></p>
<table id="tableLog" class="table table-stripped">
	<thead>
		<tr>
			<th>URL</th>
			<th>Short URL</th>
			<th></th/>
		</tr>
	</thead>
	<tbody id="tbodyLog">

	</tbody>
</table>

<script>

var divActiveZone   = document.getElementById('divActiveZone');
var inputUrlSource  = document.getElementById('inputUrlSource');
var inputUrlTarget  = document.getElementById('inputUrlTarget');
var buttonShorten   = document.getElementById('buttonShorten');
var pNotify         = document.getElementById('pNotify');
var tbodyLog        = document.getElementById('tbodyLog');

/**
* Displays a temporary notification alerting the user
**/
var notify = function(text) {
	pNotify.innerHTML = text;
	pNotify.className = '';
	setTimeout(function() {
		pNotify.className = 'hiding';
	}, 4000);
}

/**
* Adds a processed sourceUrl targetUrl pair to the log
*/
var addToLog = function(id, sourceUrl, targetUrl) {

	var row = document.createElement("tr");
	row.id = 'row-id' + id;

	var col1 = document.createElement("td");
	col1.innerHTML = '<a target="_blank" href="' + sourceUrl + '">' + sourceUrl + '</a>';

	var col2 = document.createElement("td");
	col2.innerHTML = '<a target="_blank" href="' + targetUrl + '">' + targetUrl + '</a>';

	var col3 = document.createElement("td");

	var deleteButton = document.createElement("a");
	deleteButton.innerHTML = 'x';
	deleteButton.className = 'btn btn-danger deleteButton';
	deleteButton.onclick = function() {
		deletePair(id);
	}

	//Construct table
	col3.appendChild(deleteButton);
	row.appendChild(col1);
	row.appendChild(col2);
	row.appendChild(col3);
	tbodyLog.insertBefore(row, tbodyLog.firstChild);

	//Reset input fields
	inputUrlTarget.value = '';
	inputUrlSource.value = '';
	inputUrlSource.focus();
}

/**
* Load previous URL pairs created
**/
var loadPrevious = function() {
	var ajax = new XMLHttpRequest();
	ajax.open('GET', '?controller=api&method=getPrevious', true);
	ajax.onreadystatechange = function() {
		if (ajax.readyState === XMLHttpRequest.DONE) {
			if (ajax.readyState == 4 && ajax.status === 200) {
				var resp = JSON.parse(ajax.responseText);
				if (resp.error) {
					notify(resp.reason);
				} else {
					for(var i=0; i<resp.items.length; i++){
						var item = resp.items[i];
						addToLog(item.id, item.sourceUrl, base + "?" + item.targetUrl);
					}
				}
			} else {
				notify('There was an error loading previous URLs');
				console.error('Error loading previous URLs ', ajax.responseText);
			}
		}
	}
	ajax.send();
}

/**
* Performs an ajax request to delete a pair.
* Only works if pair deleted by original creator
*/
var deletePair = function(id) {
	var ajax = new XMLHttpRequest();
	ajax.open('GET', '?controller=api&method=deletePair&id=' + id, true);
	ajax.onreadystatechange = function() {
		if (ajax.readyState === XMLHttpRequest.DONE) {
			if (ajax.readyState == 4 && ajax.status === 200) {
				var resp = JSON.parse(ajax.responseText);
				if (resp.error) {
					notify(resp.reason);
				} else {
					document.getElementById('row-id' + id).innerHTML = '';
				}
			} else {
				notify('There was an error deleting that item.');
				console.error('Error loading deleting item', ajax.responseText);
			}
		}
	}
	ajax.send();
}

/**
* Performs ajax request, resulting in an error notification or a successful
* request added to the simplified URLs log
**/
var submitRequest = function(sourceUrl, targetUrl) {
	var ajax = new XMLHttpRequest();
	var params = 'sourceUrl=' + sourceUrl + '&targetUrl=' + targetUrl;

	ajax.open('POST', '?controller=api&method=submitRequest', true);
	ajax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

	ajax.onreadystatechange = function() {
		if (ajax.readyState === XMLHttpRequest.DONE) {
			if (ajax.readyState == 4 && ajax.status === 200) {
				var resp = JSON.parse(ajax.responseText);
				if (resp.error) {
					notify(resp.reason);
				} else {
					addToLog(resp.id, resp.sourceUrl, base + '?' + resp.targetUrl);
				}
			} else {
				notify('There was an error submitting your request');
				console.error('Error submitting request ', ajax.responseText);
			}
		}
	}

    ajax.send(params);
}

// Add listener for enter-key submission
divActiveZone.addEventListener("keyup", function(e) {
    e.preventDefault();
    if (e.keyCode == 13) {
        buttonShorten.click();
    }
});

// Shorten button action
buttonShorten.onclick = function() {
	var sourceVal = inputUrlSource.value.trim().toLowerCase();
	var targetVal = inputUrlTarget.value.trim().toLowerCase();

	//Check if URL is empty
	if (!sourceVal){
		notify('Please enter a link to shorten.');
		return;
	}

	//Prepend http to links if undefined protocol
	if (sourceVal.substr(0,2) === '//') {
		sourceVal = 'http:' + sourceVal;
	} else if (sourceVal.substr(0,4) !== 'http') {
		sourceVal = 'http://' + sourceVal;
	}
	if (/^[ -~]+$/.test(sourceVal) == false) {
		notify('This URL shortner does not support special characters in URLs.');
		return false;
	}

	//Perform field validation
	if (validate({website: sourceVal}, {website: {url: true}}) === undefined) {
		if (targetVal !== '') {
			if (/^[a-z0-9]*$/.test(targetVal) == false) {
				notify('Custom URLs may only contain letters and numbers.');
				return false;
			} else {
				if(targetVal.length < 4 || targetVal.length > 20){
					notify('Custom URLs must be between 4 and 20 characters.');
					return false;
				}
			}
		}
	} else {
		notify('Invalid link, please ensure the link is valid.');
		return false;
	}

	//Submit via Ajax
	submitRequest(sourceVal, targetVal);
}

//Firstly, load previous pairs for this session
loadPrevious();

</script>
