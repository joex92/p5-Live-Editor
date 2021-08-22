<html>
	<head>
		<title>Joe X!'s p5 Live Editor</title>
    <style>
		body {
			padding: 0; 
			margin: 0; 
			/*background-color: #000000;*/
		}

		main {
			position: fixed;
			z-index: 0;
			top: 0;
			left: 0;
			vertical-align: top;
			padding: 0;
			margin: 0;
			/*NO SELECT*/
			-webkit-touch-callout: none; /* iOS Safari */
			-webkit-user-select: none; /* Safari */
			-khtml-user-select: none; /* Konqueror HTML */
			-moz-user-select: none; /* Firefox */
			-ms-user-select: none; /* Internet Explorer/Edge */
			user-select: none; /* Non-prefixed version, currently supported by Chrome and Opera */
		} 

		pre {
			display: inline;
			font-family: monospace;
			white-space: pre;
			margin: 0;
		}

		/* unvisited link */
		a:link {
		  	color: #6C7474;
  			text-decoration: none;
		}

		/* visited link */
		a:visited {
		  	color: #6C7474;
  			text-decoration: none;
		}

		/* mouse over link */
		a:hover {
		  	color: #D3D3D3;
  			text-decoration: underline;
		}

		/* selected link */
		a:active {
		  	color: #6C7474;
  			text-decoration: none;
		}

		.popup * {
			z-index: 10;
  			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			font-style: normal;
			font-weight: normal;
		}

		.popup {
			padding: 0px;
			margin: 0px;
			/*background-color: #f1f1f1;*/
			/*border: 1px solid #d3d3d3;*/
			/*text-align: center;*/
			/*min-width: 300px;*/
			/*min-height: 150px;*/
			/*max-width: 600px;*/
			max-height: 100%;
		}

		/*Dragable */

		.popup {
  			position: fixed;
			position: absolute;
		  	/*resize: both; !*enable this to css resize*! */
		  	overflow: auto;
		  	top: 0px;
		  	left: 0px;
		}

		.popup-header {
			padding: 0px;
			cursor: move;
			z-index: 10;
			background-color: rgba(32,33,36,0.75);
			color: #fff;
			font-size: 12px;
			/*NO SELECT*/
			-webkit-touch-callout: none; /* iOS Safari */
			-webkit-user-select: none; /* Safari */
			-khtml-user-select: none; /* Konqueror HTML */
			-moz-user-select: none; /* Firefox */
			-ms-user-select: none; /* Internet Explorer/Edge */
			user-select: none; /* Non-prefixed version, currently supported by Chrome and Opera */
		}

		/*Resizeable*/

		/*.popup .resizer-right {
		  width: 5px;
		  height: 100%;
		  background: transparent;
		  position: absolute;
		  right: 0;
		  bottom: 0;
		  cursor: e-resize;
		}

		.popup .resizer-bottom {
		  width: 100%;
		  height: 5px;
		  background: transparent;
		  position: absolute;
		  right: 0;
		  bottom: 0;
		  cursor: n-resize;
		}

		.popup .resizer-both {
		  width: 5px;
		  height: 5px;
		  background: transparent;
		  z-index: 10;
		  position: absolute;
		  right: 0;
		  bottom: 0;
		  cursor: nw-resize;
		}*/

		#livedraw {
			/*font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;*/
			font-family: monospace;
  			font-weight:bold;
			background-color: rgba(255,255,255,0.5);
			/*width: 100%;*/
			min-width:20em;
			min-height:10em;
			max-height: 100vh;
			max-width: 100vw;
			padding: 0;
			margin: 0;
		}

		#console {
			white-space: pre-line;
			background-color: rgba(255,255,255,0.75);
			font-size: 12px;
			/*font-family: Baskerville, "Baskerville Old Face", "Hoefler Text", Garamond, "Times New Roman", serif;*/
		}
	</style>
	<script id="handlers">
    	let tmpfc=0, fpssum=0, ldraw=()=>{}, fdraw="", consolelog, newline, downloadSketch=()=>{};
		window.onload = function() {
			initDragElement();
			// initResizeElement();
			document.getElementById('livedrawtoggle').onclick = (e) => {
				if (document.getElementById('livedraw').hidden) {
					document.getElementById('fps').style["position"] = "absolute";
					document.getElementById('livedrawtoggle').textContent = "◢"
					document.getElementById('livedraw').hidden = false;
				} else {
					document.getElementById('fps').style["position"] = "static";
					document.getElementById('livedrawtoggle').textContent = "◥"
					document.getElementById('livedraw').hidden = true;
				}
			}

			// Enable draw editor once window has loaded
			document.getElementById('livedraw').disabled = false;
			HTMLTextAreaElement.prototype.getCaretPosition = function () { //return the caret position of the textarea
			    return this.selectionStart;
			};
			HTMLTextAreaElement.prototype.setCaretPosition = function (position) { //change the caret position of the textarea
			    this.selectionStart = position;
			    this.selectionEnd = position;
			    this.focus();
			};
			HTMLTextAreaElement.prototype.hasSelection = function () { //if the textarea has selection then return true
			    if (this.selectionStart == this.selectionEnd) {
			        return false;
			    } else {
			        return true;
			    }
			};
			HTMLTextAreaElement.prototype.getSelectedText = function () { //return the selection text
			    return this.value.substring(this.selectionStart, this.selectionEnd);
			};
			HTMLTextAreaElement.prototype.setSelection = function (start, end) { //change the selection area of the textarea
			    this.selectionStart = start;
			    this.selectionEnd = end;
			    this.focus();
			};

			var textarea = document.getElementsByTagName('textarea')[0]; 

			textarea.onkeydown = function(event) {
			    
			    //support tab on textarea
			    if (event.keyCode == 9) { //tab was pressed
			        var newCaretPosition;
			        newCaretPosition = textarea.getCaretPosition() + "    ".length;
			        textarea.value = textarea.value.substring(0, textarea.getCaretPosition()) + "    " + textarea.value.substring(textarea.getCaretPosition(), textarea.value.length);
			        textarea.setCaretPosition(newCaretPosition);
			        return false;
			    }
			    if(event.keyCode == 8){ //backspace
			        if (textarea.value.substring(textarea.getCaretPosition() - 4, textarea.getCaretPosition()) == "    ") { //it's a tab space
			            var newCaretPosition;
			            newCaretPosition = textarea.getCaretPosition() - 3;
			            textarea.value = textarea.value.substring(0, textarea.getCaretPosition() - 3) + textarea.value.substring(textarea.getCaretPosition(), textarea.value.length);
			            textarea.setCaretPosition(newCaretPosition);
			        }
			    }
			    if(event.keyCode == 37){ //left arrow
			        var newCaretPosition;
			        if (textarea.value.substring(textarea.getCaretPosition() - 4, textarea.getCaretPosition()) == "    ") { //it's a tab space
			            newCaretPosition = textarea.getCaretPosition() - 3;
			            textarea.setCaretPosition(newCaretPosition);
			        }    
			    }
			    if(event.keyCode == 39){ //right arrow
			        var newCaretPosition;
			        if (textarea.value.substring(textarea.getCaretPosition() + 4, textarea.getCaretPosition()) == "    ") { //it's a tab space
			            newCaretPosition = textarea.getCaretPosition() + 3;
			            textarea.setCaretPosition(newCaretPosition);
			        }
			    } 
			}


			// Set sketch file function
			downloadSketch=()=>{
				let libs = ["// Sketch file generated by Joe X!'s Live Editor at","// "+document.URL,"// Joe X! Web Apps: https://joex.apps.dj/"];
				for (l of document.getElementsByClassName("libs")){
					libs = libs.concat("import(\""+l.src+"\");");
				}
				let re = /([a-zA-Z 0-9]*)=/;
				let vars = [];
				for (l of fdraw.split('\n')){
					console.log(l.search("var "),l.search("let "));
					if (re.test(l) && ( l.search("var ") === -1 && l.search("let ") === -1 ) ){
						v = re.exec(l)[1].replaceAll(" ","");
						if (v !== ""){
							vars = vars.concat(v);
						}
					}
				}
				vars = Array.from(new Set(vars));
				let sketch = libs.concat(
					(
						document.getElementById("p5setup").textContent.replaceAll("\t}","}").replace("cnv;\n","cnv, "+vars.join(", ")+";\n")+
						"\n\t\tfunction draw() {\n\t\t\t"+
						fdraw.replaceAll("newline","\"\\n\"").replaceAll("\n","\n\t\t\t")+
						"\n\t\t}\n"+
						document.getElementById("p5wasmPromise").textContent.replaceAll("\t}","}")
					).replaceAll("\t\t","").split('\n')
				);
				saveStrings(sketch,"sketch"+Date.now(),"js");
			};
		}



		function initDragElement() {
		  var pos1 = 0,
		    pos2 = 0,
		    pos3 = 0,
		    pos4 = 0;
		  var popups = document.getElementsByClassName("popup");
		  var elmnt = null;
		  var currentZIndex = 100; //TODO reset z index when a threshold is passed

		  for (var i = 0; i < popups.length; i++) {
		    var popup = popups[i];
		    var header = getHeader(popup);

		    popup.onmousedown = function(e) {
		      this.style.zIndex = "" + ++currentZIndex;
		      e.stopPropagation();
		    };

		    if (header) {
		      header.parentPopup = popup;
		      header.onmousedown = dragMouseDown;
		    }
		  }

		  function dragMouseDown(e) {
		    elmnt = this.parentPopup;
		    elmnt.style.zIndex = "" + ++currentZIndex;

		    e = e || window.event;
		    // get the mouse cursor position at startup:
		    pos3 = e.clientX;
		    pos4 = e.clientY;
		    document.onmouseup = closeDragElement;
		    // call a function whenever the cursor moves:
		    document.onmousemove = elementDrag;
		  }

		  function elementDrag(e) {
		    if (!elmnt) {
		      return;
		    }

		    e = e || window.event;
		    // calculate the new cursor position:
		    pos1 = pos3 - e.clientX;
		    pos2 = pos4 - e.clientY;
		    pos3 = e.clientX;
		    pos4 = e.clientY;
		    // set the element's new position:
		    elmnt.style.top = elmnt.offsetTop - pos2 + "px";
		    elmnt.style.left = elmnt.offsetLeft - pos1 + "px";
		  }

		  function closeDragElement() {
		    /* stop moving when mouse button is released:*/
		    document.onmouseup = null;
		    document.onmousemove = null;
		  }

		  function getHeader(element) {
		    var headerItems = element.getElementsByClassName("popup-header");

		    if (headerItems.length === 1) {
		      return headerItems[0];
		    }

		    return null;
		  }
		}

		// function initResizeElement() {
		//   var popups = document.getElementsByClassName("popup");
		//   var element = null;
		//   var startX, startY, startWidth, startHeight;

		//   for (var i = 0; i < popups.length; i++) {
		//     var p = popups[i];

		//     var right = document.createElement("div");
		//     right.className = "resizer-right";
		//     p.appendChild(right);
		//     right.addEventListener("mousedown", initDrag, false);
		//     right.parentPopup = p;

		//     var bottom = document.createElement("div");
		//     bottom.className = "resizer-bottom";
		//     p.appendChild(bottom);
		//     bottom.addEventListener("mousedown", initDrag, false);
		//     bottom.parentPopup = p;

		//     var both = document.createElement("div");
		//     both.className = "resizer-both";
		//     p.appendChild(both);
		//     both.addEventListener("mousedown", initDrag, false);
		//     both.parentPopup = p;
		//   }

		//   function initDrag(e) {
		//     element = this.parentPopup;

		//     startX = e.clientX;
		//     startY = e.clientY;
		//     startWidth = parseInt(
		//       document.defaultView.getComputedStyle(element).width,
		//       10
		//     );
		//     startHeight = parseInt(
		//       document.defaultView.getComputedStyle(element).height,
		//       10
		//     );
		//     document.documentElement.addEventListener("mousemove", doDrag, false);
		//     document.documentElement.addEventListener("mouseup", stopDrag, false);
		//   }

		//   function doDrag(e) {
		//     element.style.width = startWidth + e.clientX - startX + "px";
		//     element.style.height = startHeight + e.clientY - startY + "px";
		//   }

		//   function stopDrag() {
		//     document.documentElement.removeEventListener("mousemove", doDrag, false);
		//     document.documentElement.removeEventListener("mouseup", stopDrag, false);
		//   }
		// }
	</script>
    <script class="libs" src="https://github.com/processing/p5.js/releases/download/v1.3.1/p5.js" type="text/javascript"></script>
	<?
		if (strlen($_GET['libs']) > "0"){
			$libs = explode('|',$_GET['libs']); // json_encode( $libs );
			foreach ($libs as $lib) {
				echo '<script class="libs" src="'.$lib.'"></script>';
			}
		}
	?>
    <script class="libs" src="https://cdn.jsdelivr.net/npm/p5.wasm@0.2.1/dist/p5.wasm.js"></script>
	<script id="p5setup">
		// p5.wasm: This is to stop global mode from starting automatically
		p5.instance = true;

		let cnv;
		<?
			if (strlen($_GET['global']) > "0"){
				echo $_GET['global'];
			}
		?>

		function preload(){
			<?
				if (strlen($_GET['preload']) > "0"){
					echo $_GET['preload'];
				}
			?>
		}

		function setup(){
			cnv = createCanvas(innerWidth, innerHeight<? if(($_GET['render'] === 'WEBGL') || ($_GET['render'] === 'webgl') || ($_GET['render'] === 'WGL') || ($_GET['render'] === 'wgl') || ($_GET['render'] === '3D') || ($_GET['render'] === '3d') || ($_GET['render'] === 'WebGL')){echo ', WEBGL';} ?>);
			<?
				if (strlen($_GET['setup']) > "0"){
					echo $_GET['setup'];
				}
			?>
		}
	</script>
	<script id="p5livedraw">
		function draw(){
			if (millis()%1000 < 10){
				tmpfc = frameCount;
				fpssum = 0;
			} else {
				fpssum = wasm.round_decimal(fpssum + frameRate(),2);
			}
			document.getElementById('fps').textContent = " FPS:"+wasm.round_decimal(fpssum/wasm.constrain(frameCount-tmpfc,1,Infinity),2)+" ";

			if (document.getElementById("console").offsetWidth > document.getElementById("livedraw").offsetWidth) {
				document.getElementById("livedraw").style["width"] = "100%";
			}
			document.getElementById("livedraw").style["max-height"] = innerHeight - document.getElementById("console").offsetHeight - document.getElementById("popupdiv-header").offsetHeight;

			if (document.getElementsByClassName("popup")[0].offsetLeft < 0 || document.getElementsByClassName("popup")[0].offsetLeft > innerWidth-document.getElementsByClassName("popup")[0].offsetWidth){
				document.getElementsByClassName("popup")[0].style["left"] = wasm.constrain(document.getElementsByClassName("popup")[0].offsetLeft,0,innerWidth-document.getElementsByClassName("popup")[0].offsetWidth)+"px";
			}
			if (document.getElementsByClassName("popup")[0].offsetTop < 0 || document.getElementsByClassName("popup")[0].offsetTop > innerHeight-document.getElementsByClassName("popup")[0].offsetHeight){
				document.getElementsByClassName("popup")[0].style["top"] = wasm.constrain(document.getElementsByClassName("popup")[0].offsetTop,0,innerHeight-document.getElementsByClassName("popup")[0].offsetHeight)+"px";
			}

			cnv.resize(innerWidth, innerHeight);
			
			document.getElementById("console").style["color"] = "rgba(180,188,197,0.75)"; // "#A7B1BC";
			document.getElementById("console").style["background-color"] = "rgba(32,33,36,0.75)"; // "#18191B";

			if (document.getElementById('livedraw').value.replace(" ","").length > 0){
				document.getElementById('livedrawcontent').textContent = "//save";
			} else {
				document.getElementById('livedrawcontent').textContent = "";
			}

			try {
				consolelog = [];
				fdraw = document.getElementById('livedraw').value;
				ldraw = Function(fdraw.replace("console.log","consolelog=consolelog.concat").replaceAll("console.log","consolelog=consolelog.concat(\"\\r\\n\");consolelog=consolelog.concat"));
				ldraw();
				for (let i = 0; i < consolelog.length; i++) {
					if (consolelog[i] !== undefined){
						if (consolelog.to_string !== undefined){
							consolelog[i] = consolelog[i].to_string();
					 	} else {
					 		consolelog[i] = consolelog[i].toString();
					 	}
					}
				}
			} catch(e) {
				document.getElementById("console").style["color"] = "rgba(255,128,128,0.75)"; // "#E95F64";
				document.getElementById("console").style["background-color"] = "rgba(41,0,0,0.75)"; // "#1E0000";
				console.log(e);
				consolelog = e;
			}

			newline = '\r\n';

			try {
				if (Array.isArray(consolelog)) {
					document.getElementById('console').textContent = consolelog.join(', ').replaceAll(", "+newline+", ",newline);
				} else {
					document.getElementById('console').textContent = consolelog.toString();
				}
			} catch(e) {
				document.getElementById("console").style["color"] = "rgba(255,128,128,0.75)"; // "#E95F64";
				document.getElementById("console").style["background-color"] = "rgba(41,0,0,0.75)"; // "#1E0000";
				document.getElementById('console').textContent = e.toString();
			}
		}
	</script>
	<script id="p5wasmPromise">
		// p5.wasm: Wait for promise to resolve then start p5 sketch
		window.p5WasmReady.then(() => {
			new p5();
			<?
				if (strlen($_GET['draw']) > "0"){
					echo 'document.getElementById("livedraw").value="'.$_GET['draw'].'";';
				}
			?>
		});
    </script>
	</head>
	<body>
		<main></main>
		<div id="popupdiv" class="popup"><div id="popupdiv-header" class="popup-header">&nbsp;<span id="livedrawtoggle" style="cursor: pointer;">◢</span>&nbsp;<span style="color: #9A7BCA;">function</span>&nbsp;<span style="color: #51AED7;">draw</span>(){&nbsp;<a href="javascript:downloadSketch();"><pre id="livedrawcontent" style="font-size: 90%; font-style: italic;">…</pre></a>&nbsp;}<pre id="fps" style="position: absolute;text-align: right; top: 0.25em; right: 0;"></pre></div>
		<textarea disabled autofocus id="livedraw" wrap = "off">console.log(Date());
if (cnv.isP3D){
	woff = -innerWidth/2;
	hoff = -innerHeight/2;
} else {
	woff = 0;
	hoff = 0;
}
cb = color("hsl("+wasm.round((millis()/36)%360)+",100%,50%)");
cf = color("hsl("+wasm.round(((millis()/36)+180)%360)+",100%,50%)");
//background(cb);
document.body.style["backgroundColor"] = "#000000";
noStroke();
if (mouseIsPressed){
	noCursor();
	fill(wasm.abs(wasm.round((millis()/(36))%512)-256));
	ellipse(mouseX+woff,mouseY+hoff,innerWidth/100,innerHeight/100);
} else {
	cursor();
	fill(cf);
	ellipse(wasm.noise2d(millis()/100,0)*innerWidth+woff,wasm.noise2d(0,millis()/100)*innerHeight+hoff,innerWidth/100,innerHeight/100);
}
console.log(cb,cf,newline,mouseX,mouseY);</textarea>
		<div id="console">
		</div></div>
	</body>
</html>