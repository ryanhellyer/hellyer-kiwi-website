const config = {
    'encryptionConfirmationKey': 'encryptionConfirmationKey|',
    'encryptionSalt': '9f14b082ad224f50be6e3cbb246985a15f426b13cf79a0957f8dda790916ecb9',
    'blockTemplate': `
<li>
    <input type="text" value="{{name}}">
    <input type="hidden" value="{{name}}">
    <input class="password" type="password" value="" placeholder="Enter password">
    <div class="editableContent" contenteditable="true"></div>
    <div class="encryptedContent">{{encryptedContent}}</div>
    <button class="save">Save</button>
    <button class="delete">Delete</button>
    <input class="new-password" type="password" value="" placeholder="Enter new password">
    <p></p>
</li>`
}


document.addEventListener('DOMContentLoaded', async function() {
    const ajax = new Ajax();

    // Populate the blocks.
    let items = await ajax.getItems();
    const blocks = document.getElementById('blocks');
    if(blocks) {
        for (const item of items) {
            blocks.innerHTML += getBlock(item);
        }
    }



    const inputHelper = new InputHelper();
    const animations = new Animations();
    const encryption = new Encryption();

    const inputs = new Inputs(inputHelper, animations, ajax, encryption);

    const eventHandlers = new EventHandlers(inputs);












    function getBlock(item: { encryptedContent: string; name: string }): string {
        let HTML = config.blockTemplate;

        HTML = HTML.replaceAll('{{encryptedContent}}', item.encryptedContent);
        HTML = HTML.replaceAll('{{name}}', item.name);

        return HTML;
    }

});
