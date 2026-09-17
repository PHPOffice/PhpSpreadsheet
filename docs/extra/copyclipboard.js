document.addEventListener("DOMContentLoaded", function () {
    // Locate all code blocks inside the theme highlights
    const codeBlocks = document.querySelectorAll("pre");

    codeBlocks.forEach(function (block) {
        // Create the button element
        const button = document.createElement("button");
        button.className = "copy-code-button";
        button.type = "button";
        button.innerText = "Copy";

        // Extract raw code text from the pre/code block
        const pre = block;
        const code = pre.getElementsByTagName('code');
        if (code.length == 1) {
            button.addEventListener("click", function () {
                const textToCopy = code[0].innerText || code[0].textContent;

                navigator.clipboard.writeText(textToCopy).then(function () {
                    button.innerText = "Copied!";
                    setTimeout(function () {
                        button.innerText = "Copy";
                    }, 2000);
                }).catch(function (error) {
                    button.innerText = "Error";
                    console.error("Failed to copy text: ", error);
                });
            });

            // Append the button into the container block
            block.setAttribute("style", "position: relative;");
            block.appendChild(button);
        }
    });
});
