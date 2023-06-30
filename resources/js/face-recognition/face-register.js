const csrfToken = jQuery('meta[name="csrf-token"]').attr('content');
const loginToken = jQuery('meta[name="login-token"]').attr('content');
const xhrHeaders = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'Authorization' : 'Bearer ' + loginToken, 
    'X-CSRF-Token': csrfToken
};
const video = document.getElementById('user-video');
let canvas;
let detectionInterval;
let getSnapshot = null;
let registerReady = false;

Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri('/js/face-api/models'),
    faceapi.nets.faceExpressionNet.loadFromUri('/js/face-api/models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('/js/face-api/models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('/js/face-api/models'),
    faceapi.nets.ssdMobilenetv1.loadFromUri('/js/face-api/models'),
]).then(startVideo);

const frSel = {
    get registerBtn() {return `div.register button`},
};

function processingNotif(element) {
    $(element).notify('Processing...', { 
        className: 'info',
        autoHide: false,
        arrowSize: 10,
        position: 'top center',
    });
}

function successNotif(element) {
    $(element).notify('Registration Successful!', { 
        className: 'success',
        autoHide: true,
        arrowSize: 10,
        position: 'top center',
    });
}

function errorNotif(element, err) {
    $(element).notify(err, { 
        className: 'error',
        position: 'top center',
        autoHideDelay: 10000,
        arrowSize: 10,
    });
}

async function validateSnapshot(base64Img) {
    const img = new Image();
    img.src = base64Img;
    const detections = await faceapi.detectSingleFace(img)
        .withFaceLandmarks()
        .withFaceDescriptor();
    if (detections?.descriptor) {
        return true;
    }
    return false;
}

function takeSnapshot() {
    processingNotif(frSel.registerBtn);
    $(async() => {
        let context;
        let width = video.offsetWidth, 
            height = video.offsetHeight;
        canvas = canvas || document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, width, height);
        const snapshotBase64 = canvas.toDataURL('image/png');
        pauseVideo();
        const isValidSnapshot = await validateSnapshot(snapshotBase64);
        if (isValidSnapshot) {
            fetch("/api/face-recognition/save-image", {
                method: "POST",
                credentials: "same-origin",
                headers: xhrHeaders,
                body: JSON.stringify({ image: snapshotBase64 })
            }).then(response => {
                if (response.ok) {
                    response.json().then((json) => {
                        successRegistration();
                    })
                } else {
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                errorNotif(frSel.registerBtn, 'Something went wrong')
                playVideo();
                console.log('Something went wrong,', err);
            });
        } else {
            errorNotif(frSel.registerBtn, 'Please make sure your image is clear!');
            playVideo();
        }
    });
}

function startVideo() {
    if (navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                video.srcObject = stream;
                detectVideo();
            })
            .catch(function (error) {
                console.log("Something went wrong!");
            });
    }
}

function pauseVideo() {
    getSnapshot = null;
    $(video).off();
    clearInterval(detectionInterval);
    canvas.remove();
    video.pause();
}

function playVideo() {
    video.play();
    detectVideo();
    disabledRegisterButton(false);
}

function stopVideo() {
    video.srcObject.getTracks().forEach(function(track) {
        track.stop();
    });
    video.srcObject = null;
}

function successRegistration() {
    disabledRegisterButton(true);
    successNotif(frSel.registerBtn);
    stopVideo();
    $('div.recognition').addClass('bg-gray-500');
    $('div.recognition').html(`
        <div class="bg-gray-500 p-40 text-center text-xl font-black text-white">
            <span>Your registration is successful. You can register again if you experience issues regarding Face Recognition.</span>
        </div>
    `);
}

function disabledRegisterButton(isDisabled) {
    $(frSel.registerBtn).prop('disabled', isDisabled);
    if (isDisabled) {
        $(frSel.registerBtn).removeClass('bg-blue-500');
        $(frSel.registerBtn).addClass('bg-gray-400');
    } else {
        $(frSel.registerBtn).removeClass('bg-gray-400');
        $(frSel.registerBtn).addClass('bg-blue-500');
    }
}

function detectVideo() {
    $(video).on('playing', async() => {
        if (canvas) canvas.remove()
        // create canvas
        canvas = faceapi.createCanvasFromMedia(video);
        $('.face-recognition.register div.recognition').append(canvas);
        // faceapi dimensions
        const displaySize = { width: video.width, height: video.height };
        faceapi.matchDimensions(canvas, displaySize);
        // draw face detections
        detectionInterval = setInterval(async () => {
            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceExpressions();
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            faceapi.draw.drawDetections(canvas, resizedDetections);
            faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
            faceapi.draw.drawFaceExpressions(canvas, resizedDetections);
            if (!detections.length || (detections.length > 1)) return;
            if (getSnapshot) {
                getSnapshot = false;
                takeSnapshot();
            }
            // enable register button
            const registerButtonIsDisabled = $(frSel.registerBtn).attr('disabled');
            if (!registerReady && registerButtonIsDisabled) {
                registerReady = true;
                disabledRegisterButton(false);
            }
        }, 100);
        getSnapshot = false;
    });
}

$(document).on('click', frSel.registerBtn, function () {
    if (getSnapshot != null) {
        disabledRegisterButton(true);
        setTimeout(() => getSnapshot = true, 1500);
    }
});