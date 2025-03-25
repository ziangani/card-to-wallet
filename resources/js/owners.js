$(document).ready(function() {
    const $ownersContainer = $('#owners-container');
    const $addOwnerBtn = $('#add-owner-btn');
    
    // Add new owner
    $addOwnerBtn.on('click', function() {
        const $ownerSections = $('.owner-section');
        const newIndex = $ownerSections.length;
        
        // Clone first owner section
        const $template = $ownerSections.first().clone();
        
        // Update title
        $template.find('.owner-title').text(`Owner ${newIndex + 1}`);
        
        // Add remove button
        const $titleDiv = $template.find('.d-flex');
        const $removeBtn = $(`
            <button type="button" class="btn btn-icon btn-sm btn-danger remove-owner">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </button>
        `);
        $titleDiv.append($removeBtn);
        
        // Clear input values
        $template.find('input, select').val('');
        
        // Add the new section
        $ownersContainer.append($template);
        
        // Limit to 5 owners
        if ($ownerSections.length >= 4) {
            $addOwnerBtn.hide();
        }
    });
    
    // Remove owner
    $ownersContainer.on('click', '.remove-owner', function() {
        const $section = $(this).closest('.owner-section');
        $section.remove();
        
        // Update remaining owner titles
        $('.owner-section').each(function(index) {
            $(this).find('.owner-title').text(`Owner ${index + 1}`);
        });
        
        // Show add button if below limit
        if ($('.owner-section').length < 5) {
            $addOwnerBtn.show();
        }
    });
});
