window.onload = () => {
    moveImage('enemy_kart', enemy_coordinates, 'enemy');
    setInitialPosition("flag", coordinate);
    setInitialPosition("enemy_kart", coordinate);
    setInitialPosition("player_kart", coordinate);
}

/**
 *  @brief Send value of the terminal to the process and display previous user input and their comparaison with the answer of their question.
 *
 * @param questionResponse (String) answer of the current question.
 */
function sendCommand(questionResponse) {
    const input = document.getElementById("terminal-input");
    const output = document.getElementById("terminal-output");
    const response = processCommand(input.value, questionResponse);
    if (input.value && response[0] !== 'clear') {
        output.innerHTML += '<span style="color: cornflowerblue">NetKart:~$</span>  ' + input.value + "<br>";
        output.innerHTML += "<span style=\"color: " + response[1] + "\">" + response[0] + "</span>" + "<br>";
        input.value = "";
        scroll(output);
    } else if (response[0] === "clear") {
        output.innerHTML = '';
        input.value = "";
    }
}

/**
 * @brief Display the modal when the player win or lose.
 *
 * @return void
 */
function displayModal() {
    const modal = document.getElementById("modal");
    const close = document.getElementById("modal-close");
    const returnBtn = document.getElementById("modal-return") || {};
    const restartBtn = document.getElementById("modal-restart");

    modal.style.display = "block";

    close.onclick = function () {
        modal.style.display = "none";
    }

    returnBtn.onclick = function () {
        modal.style.display = "none";
        window.location.assign("themes.php");
    }

    restartBtn.onclick = function () {
        window.location.reload();
    }
}

/**
 * @brief Move image to next coordinates.
 *
 * @param imageId (String) Image id.
 * @param coordinates (Array) Coordinates of the circuit left.
 * @param status (String) Determine if the image is the player or the enemy one.
 */
function moveImage(imageId, coordinates, status) {
    if (!game) {
        if (status === "ally")
        if (coordinates.length === 0) {
            return;
        }
        let image = document.getElementById(imageId);
        let delay = status === "enemy" ? 100 : 10;
        let count = 0
        coordinates[0].forEach(([x, y], i) => {
            setTimeout(() => {
                image.style.marginLeft = x + "%";
                image.style.marginTop = y + "%";
                count++;
                if (count === coordinates[0].length) {
                    coordinates.shift();
                    if (coordinates.length === 0) {
                        setVictory('modal-body', status)
                    } else if (status === "enemy") {
                        setTimeout(() => {
                            moveImage(imageId, coordinates, status);
                        }, delay);
                    }
                }
            }, i * delay);
        });
    }
}

/**
 * @brief Move the player image when the player answer is correct.
 *
 * @param imageId (String) Image id.
 * @param coordinates (Array) Coordinates of the circuit left.
 * @param status (String) Determine if the image is the player or the enemy one.
 */
function correctAnswer(imageId, coordinates, status) {
    moveImage(imageId, coordinates, status);
    setTimeout(() => {
        moveImage(imageId, coordinates, status);
    }, 400 * 6);
}

/**
 * @brief Set image to the initial position of the circuit.
 *
 * @param imageId (String) Image id.
 * @param coordinates (Array) Initial position of the circuit.
 */
function setInitialPosition(imageId, coordinates) {
    if (coordinates.length === 0) {
        return;
    }
    let image = document.getElementById(imageId);
    image.style.marginLeft = coordinates[0][0] + "%";
    image.style.marginTop = coordinates[0][1] + "%";
}

/**
 * @brief Generates coordinates between starting and ending ones.
 *
 * @param startX (Int) X starting coordinate.
 * @param startY (Int) Y starting coordinate.
 * @param endX (Int) X ending coordinate.
 * @param endY (Int) X ending coordinate.
 * @return {*[]} Generated coordinates.
 */
function generateCoordinates(startX, startY, endX, endY) {
    const coordinates = [];
    const xStep = (endX - startX) / 200;
    const yStep = (endY - startY) / 200;

    for (let i = 0; i < 200; i++) {
        const x = startX + i * xStep;
        const y = startY + i * yStep;
        coordinates.push([x, y]);
    }
    return coordinates;
}

/**
 * @brief Generate coordinate of an entity like the player or the enemy.
 *
 * @param coordinates (Array) Coordinates of the selected circuit.
 * @return {*[]} Generated coordinates of an entity.
 */
function populateArray(coordinates) {
    let arrayCoordinates = [];
    for (let i = 0; i < coordinates.length - 1; i++) {
        arrayCoordinates.push(generateCoordinates(coordinates[i][0], coordinates[i][1], coordinates[i + 1][0], coordinates[i + 1][1]));
    }
    return arrayCoordinates;
}

/**
 * @brief Scroll to the last line of the terminal
 *
 * @param item (HTMLDivElement) Game Terminal.
 */
function scroll(item) {
    item.scrollTop = item.scrollHeight - item.clientHeight;
}
