const config = {
    'encryptionConfirmationKey': 'encryptionConfirmationKey|',
    'blockTemplate': `
<li>
    <input type="text" value="{{name}}">
    <input type="hidden" value="{{name}}">
    <input type="password" value="" placeholder="Enter password">
    <div class="editableContent" contenteditable="true"></div>
    <div class="encryptedContent">{{encryptedContent}}</div>
    <button class="save">Save</button>
    <button class="delete">Delete</button>
<p></p>
</li>`
}


document.addEventListener('DOMContentLoaded', async function() {
    const loadItems = new LoadItems();
    const encryption = new Encryption();


    let items = await loadItems.getItems();


    const blocks = document.getElementById('blocks');
    if(blocks) {
        for (const item of items) {
            blocks.innerHTML += getBlock(item);
        }
    }











    function getBlock(item: Array<any>): string {
        let HTML = config.blockTemplate;

        HTML = HTML.replace('{{encryptedContent}}', item.encryptedContent);
        HTML = HTML.replace('{{name}}', item.name);

        return HTML;
    }

});
