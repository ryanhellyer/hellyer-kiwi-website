const encryptionConfirmationKey = 'encryptionConfirmationKey|';

document.addEventListener('DOMContentLoaded', function() {
    new e2eEventHandlers(
        new e2eInputs(
            new e2eItemHandler,
            new e2eAnimations(),
            new e2eAjax(),
            new e2eEncryption
        )
    );
});