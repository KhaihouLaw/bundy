
const csrfToken = jQuery('meta[name="csrf-token"]').attr('content');
const loginToken = jQuery('meta[name="login-token"]').attr('content');
const xhrHeaders = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'Authorization' : 'Bearer ' + loginToken, 
    'X-CSRF-Token': csrfToken
};
const video = document.getElementById('user-video')
let canvas = null;
let faceMatcher = null;
let clockedUsers = [];

Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri('/js/face-api/models'),
    faceapi.nets.faceExpressionNet.loadFromUri('/js/face-api/models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('/js/face-api/models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('/js/face-api/models'),
    faceapi.nets.ssdMobilenetv1.loadFromUri('/js/face-api/models'),
]).then(startVideo);

async function startVideo() {
    const labeledFaceDescriptors = await loadLabeledImages();
    faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.95);
    if (navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                video.srcObject = stream;
            })
            .catch(function (error) {
                console.log("Something went wrong!");
            });
    }
}

function stopVideo() {
    video.srcObject.getTracks().forEach(function(track) {
        track.stop();
    });
    video.srcObject = null;
}

video.addEventListener('playing', () => {
    // create canvas
    canvas = faceapi.createCanvasFromMedia(video);
    $('.face-recognition div.recognition').append(canvas);
    // faceapi dimensions
    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(canvas, displaySize);
    // draw face detections
    setInterval(async () => {
        // detect all faces
        const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.85 }))
            .withFaceLandmarks()
            .withFaceExpressions()
            .withFaceDescriptors();
        const resizedDetections = faceapi.resizeResults(detections, displaySize);
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
        // if we have faces to compare
        if (faceMatcher) {
            const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));
            let userIds = [];
            results.forEach((metadata, i) => {
                const box = resizedDetections[i].detection.box;
                let label;
                try {
                    label = JSON.parse(metadata.label);
                    const userId = parseInt(label.userId);
                    if (!clockedUsers.includes(userId)) {
                        userIds.push(userId);
                        clockedUsers.push(userId);
                    }
                } catch (error) {
                    label = {userName: 'unknown'};
                }
                const drawBox = new faceapi.draw.DrawBox(box, { label: label.userName });
                drawBox.draw(canvas);
            });
            if (userIds.length != 0) {
                clock(userIds);
            }
        }
    }, 100);
});

function loadLabeledImages() {
    return new Promise((resolve, reject) => {
        fetch("/api/face-recognition/labeled-images", {
            method: "GET",
            credentials: "same-origin",
            headers: xhrHeaders,
        }).then(response => {
            if (response.ok) {
                response.json().then((json) => {
                    const promises = [];
                    Object.keys(json).forEach(item => {
                        promises.push((async () => {
                            const img = new Image();
                            img.src = json[item].image;
                            const detections = await faceapi.detectSingleFace(img)
                                .withFaceLandmarks()
                                .withFaceDescriptor();
                            if (detections?.descriptor) {
                                const descriptions = [detections.descriptor];
                                const metadata = JSON.stringify({ userId: json[item].user_id, userName: json[item].user_name});
                                return new faceapi.LabeledFaceDescriptors(metadata, descriptions);
                            } else {
                                throw 'Cannot recognize person in Labeled Image!';
                            }
                        })());
                    });
                    Promise.all(promises)
                        .then(result => resolve(result))
                        .catch(err => reject(err));
                })
            } else {
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            console.log('Something went wrong,', err);
            reject();
        });
    })
}

function clock(userIds) {
    const jsonData = {user_ids: userIds};
    fetch("/api/timesheet/punch-by-ids", {
        method: "POST",
        credentials: "same-origin",
        headers: xhrHeaders,
        body: JSON.stringify(jsonData),
    }).then(response => {
        if (response.ok) {
            response.json().then((json) => {
                console.log('auto clock in response', json)               
                json.data.forEach(data => {
                    let clockType;
                    if (data.punch_type == 'time-in') clockType = 'Clock In';
                    else clockType = 'Clock Out';
                    if (data.success) {
                        $('div.logs .box').append(
                            `<div>
                                <span class="text-green-500 font-black">Successfully ${clockType}</span> - 
                                ${data.user_name} - ${data.clock}
                            </div>`
                        );
                    } else {
                        $('div.logs .box').append(
                            `<div>
                                <span class="text-red-500 font-black">Failed ${clockType}</span> -
                                ${data.user_name}
                            </div>`
                        );
                    }
                });
            });
        } else {
            throw response.statusText + ' ' + response.status;
        }
    }).catch(err => {
        console.log('Something went wrong');
    });
}
