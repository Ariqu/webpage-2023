function copy1() {
    const copyMessage = document.getElementById("telefon");

    const tempTextArea = document.createElement("textarea");
    tempTextArea.value = copyMessage.textContent;

    document.body.appendChild(tempTextArea);

    // Zaznacz i skopiuj tekst do schowka
    tempTextArea.select();
    document.execCommand("copy");

    // Usuń tymczasowy element textarea
    document.body.removeChild(tempTextArea);
    copyMessage.innerHTML += " skopiowano!";
    setTimeout(function() {
        copyMessage.innerHTML = "+48 123 456 789";
    }, 1000);

}
