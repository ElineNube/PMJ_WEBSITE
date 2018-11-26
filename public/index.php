<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PMJCREATIES</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="../resources/assets/css/custom.css" type="text/css">
    <link rel="stylesheet" href="../resources/assets/css/newsletter.css" type="text/css">
    <link rel="stylesheet" href="../resources/assets/css/media-queries.css" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="../resources/assets/js/navbar.js"></script>

    <script src="https://code.createjs.com/createjs-2015.11.26.min.js"></script>
    <script src="../resources/assets/animations/PMJ_JUMBOTRON.js"></script>
    <script>
        var canvas, stage, exportRoot, anim_container, dom_overlay_container, fnStartAnimation;
        function init() {
            canvas = document.getElementById("canvas");
            anim_container = document.getElementById("animation_container");
            dom_overlay_container = document.getElementById("dom_overlay_container");
            var comp=AdobeAn.getComposition("1666048507A7CE4B9BF5049A51CFF152");
            var lib=comp.getLibrary();
            var loader = new createjs.LoadQueue(false);
            loader.addEventListener("fileload", function(evt){handleFileLoad(evt,comp)});
            loader.addEventListener("complete", function(evt){handleComplete(evt,comp)});
            var lib=comp.getLibrary();
            loader.loadManifest(lib.properties.manifest);
        }
        function handleFileLoad(evt, comp) {
            var images=comp.getImages();
            if (evt && (evt.item.type == "image")) { images[evt.item.id] = evt.result; }
        }
        function handleComplete(evt,comp) {
            //This function is always called, irrespective of the content. You can use the variable "stage" after it is created in token create_stage.
            var lib=comp.getLibrary();
            var ss=comp.getSpriteSheet();
            var queue = evt.target;
            var ssMetadata = lib.ssMetadata;
            for(i=0; i<ssMetadata.length; i++) {
                ss[ssMetadata[i].name] = new createjs.SpriteSheet( {"images": [queue.getResult(ssMetadata[i].name)], "frames": ssMetadata[i].frames} )
            }
            exportRoot = new lib.jumbotron_homepage_pmj_30();
            stage = new lib.Stage(canvas);
            //Registers the "tick" event listener.
            fnStartAnimation = function() {
                stage.addChild(exportRoot);
                createjs.Ticker.setFPS(lib.properties.fps);
                createjs.Ticker.addEventListener("tick", stage)
                stage.addEventListener("tick", handleTick)
                function getProjectionMatrix(container, totalDepth) {
                    var focalLength = 528.25;
                    var projectionCenter = { x : lib.properties.width/2, y : lib.properties.height/2 };
                    var scale = (totalDepth + focalLength)/focalLength;
                    var scaleMat = new createjs.Matrix2D;
                    scaleMat.a = 1/scale;
                    scaleMat.d = 1/scale;
                    var projMat = new createjs.Matrix2D;
                    projMat.tx = -projectionCenter.x;
                    projMat.ty = -projectionCenter.y;
                    projMat = projMat.prependMatrix(scaleMat);
                    projMat.tx += projectionCenter.x;
                    projMat.ty += projectionCenter.y;
                    return projMat;
                }
                function handleTick(event) {
                    var cameraInstance = exportRoot.___camera___instance;
                    if(cameraInstance !== undefined && cameraInstance.pinToObject !== undefined)
                    {
                        cameraInstance.x = cameraInstance.pinToObject.x + cameraInstance.pinToObject.pinOffsetX;
                        cameraInstance.y = cameraInstance.pinToObject.y + cameraInstance.pinToObject.pinOffsetY;
                        if(cameraInstance.pinToObject.parent !== undefined && cameraInstance.pinToObject.parent.depth !== undefined)
                            cameraInstance.depth = cameraInstance.pinToObject.parent.depth + cameraInstance.pinToObject.pinOffsetZ;
                    }
                    applyLayerZDepth(exportRoot);
                }
                function applyLayerZDepth(parent)
                {
                    var cameraInstance = parent.___camera___instance;
                    var focalLength = 528.25;
                    var projectionCenter = { 'x' : 0, 'y' : 0};
                    if(parent === exportRoot)
                    {
                        var stageCenter = { 'x' : lib.properties.width/2, 'y' : lib.properties.height/2 };
                        projectionCenter.x = stageCenter.x;
                        projectionCenter.y = stageCenter.y;
                    }
                    for(child in parent.children)
                    {
                        var layerObj = parent.children[child];
                        if(layerObj == cameraInstance)
                            continue;
                        applyLayerZDepth(layerObj, cameraInstance);
                        if(layerObj.layerDepth === undefined)
                            continue;
                        if(layerObj.currentFrame != layerObj.parent.currentFrame)
                        {
                            layerObj.gotoAndPlay(layerObj.parent.currentFrame);
                        }
                        var matToApply = new createjs.Matrix2D;
                        var cameraMat = new createjs.Matrix2D;
                        var totalDepth = layerObj.layerDepth ? layerObj.layerDepth : 0;
                        var cameraDepth = 0;
                        if(cameraInstance && !layerObj.isAttachedToCamera)
                        {
                            var mat = cameraInstance.getMatrix();
                            mat.tx -= projectionCenter.x;
                            mat.ty -= projectionCenter.y;
                            cameraMat = mat.invert();
                            cameraMat.prependTransform(projectionCenter.x, projectionCenter.y, 1, 1, 0, 0, 0, 0, 0);
                            cameraMat.appendTransform(-projectionCenter.x, -projectionCenter.y, 1, 1, 0, 0, 0, 0, 0);
                            if(cameraInstance.depth)
                                cameraDepth = cameraInstance.depth;
                        }
                        if(layerObj.depth)
                        {
                            totalDepth = layerObj.depth;
                        }
                        //Offset by camera depth
                        totalDepth -= cameraDepth;
                        if(totalDepth < -focalLength)
                        {
                            matToApply.a = 0;
                            matToApply.d = 0;
                        }
                        else
                        {
                            if(layerObj.layerDepth)
                            {
                                var sizeLockedMat = getProjectionMatrix(parent, layerObj.layerDepth);
                                if(sizeLockedMat)
                                {
                                    sizeLockedMat.invert();
                                    matToApply.prependMatrix(sizeLockedMat);
                                }
                            }
                            matToApply.prependMatrix(cameraMat);
                            var projMat = getProjectionMatrix(parent, totalDepth);
                            if(projMat)
                            {
                                matToApply.prependMatrix(projMat);
                            }
                        }
                        layerObj.transformMatrix = matToApply;
                    }
                }
            }
            //Code to support hidpi screens and responsive scaling.
            function makeResponsive(isResp, respDim, isScale, scaleType) {
                var lastW, lastH, lastS=1;
                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();
                function resizeCanvas() {
                    var w = lib.properties.width, h = lib.properties.height;
                    var iw = window.innerWidth, ih=window.innerHeight;
                    var pRatio = window.devicePixelRatio || 1, xRatio=iw/w, yRatio=ih/h, sRatio=1;
                    if(isResp) {
                        if((respDim=='width'&&lastW==iw) || (respDim=='height'&&lastH==ih)) {
                            sRatio = lastS;
                        }
                        else if(!isScale) {
                            if(iw<w || ih<h)
                                sRatio = Math.min(xRatio, yRatio);
                        }
                        else if(scaleType==1) {
                            sRatio = Math.min(xRatio, yRatio);
                        }
                        else if(scaleType==2) {
                            sRatio = Math.max(xRatio, yRatio);
                        }
                    }
                    canvas.width = w*pRatio*sRatio;
                    canvas.height = h*pRatio*sRatio;
                    canvas.style.width = dom_overlay_container.style.width = anim_container.style.width =  w*sRatio+'px';
                    canvas.style.height = anim_container.style.height = dom_overlay_container.style.height = h*sRatio+'px';
                    stage.scaleX = pRatio*sRatio;
                    stage.scaleY = pRatio*sRatio;
                    lastW = iw; lastH = ih; lastS = sRatio;
                    stage.tickOnUpdate = false;
                    stage.update();
                    stage.tickOnUpdate = true;
                }
            }
            makeResponsive(true,'both',false,1);
            AdobeAn.compositionLoaded(lib.properties.id);
            fnStartAnimation();
        }
    </script>

</head>

<body onload="init()">
<div class="topnav" id="myTopnav">
    <a href="#home" class="brand">PMJCREATIES</a>
    <a href="#about">CONTACT</a>
    <a href="#contact">LINKS</a>
    <a href="#news">BEURZEN</a>
    <a href="javascript:void(0);" class="icon" onclick="myFunction()">
        <i class="fa fa-bars"></i>
    </a>
</div>


    <div class="jumbotron">
        <div id="animation_container" style="background-color:rgba(159, 169, 167, 1.00); width:1000px; height:350px">
            <canvas id="canvas" width="1000" height="350" style="position: absolute; display: block; background-color:rgba(159, 169, 167, 1.00);"></canvas>
            <div id="dom_overlay_container" style="pointer-events:none; overflow:hidden; width:1000px; height:350px; position: absolute; left: 0px; top: 0px; display: block;">
            </div>
        </div>
    </div>

    <div class="static_jumbotron">
        <h1>PMJCREATIES</h1>
        <p>By Patricia, Marion & Jolande</p>
    </div>


    <div class="row">
        <div class="column" id="image1"><div class="text"><a href="creaties.php">Onze creaties</a></div></div>
        <div class="column" id="image2"><div class="text">Hoe wij werken</div></div>
        <div class="column" id="image3"><div class="text">Over ons</div></div>
    </div>

    <div class="newsletter">
        <p>Schrijf je in voor onze nieuwsbrief</p>

        <form>
            <input type="email" id="email" placeholder="Email" name="email">
            <button type="submit">Schrijf in</button>
        </form>
    </div>

    <div class="row">
        <div class="column" id="image4"></div>
        <div class="column" id="image5"><</div>
        <div class="column" id="image6"></div>
    </div>

    <footer>
        <p>PMJCREATIES ©</p>
    </footer>

    </div>
</body>
</html>
