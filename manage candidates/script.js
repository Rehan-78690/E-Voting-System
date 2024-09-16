

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
            const candidateId = this.getAttribute('data-candidate-id');
            const data = candidatesData[candidateId];

            if (data) {
                document.getElementById('viewCandidateName').textContent = data.candidate_name || '';
                document.getElementById('viewCandidateRole').textContent = data.candidate_role || '';
                document.getElementById('viewCandidateDepartment').textContent = data.department || '';
                document.getElementById('viewCandidateEmail').textContent = data.candidate_email || '';
                document.getElementById('viewCandidateNumber').textContent = data.candidate_number || '';
                document.getElementById('viewAddress').textContent = data.address || '';
                document.getElementById('viewManifesto').textContent = data.manifesto || '';
                document.getElementById('viewSocialmediaLinks').textContent = data.socialmedia_links || '';
            }
        });
    });
});
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        const candidateId = this.getAttribute('data-candidate-id');
            const data = candidatesData[candidateId];

            if (data) {
                document.getElementById('editCandidateName').value = data.candidate_name || '';
            document.getElementById('editCandidateRole').value = data.candidate_role || '';
            document.getElementById('editCandidateDepartment').value = data.department || '';
                document.getElementById('editSymbolPreview').src = data.symbol || '';
            }
    });
});
