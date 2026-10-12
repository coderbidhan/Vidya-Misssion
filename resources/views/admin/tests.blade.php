@extends('layouts.admin')
@section('title','Tests & Questions')
@section('page-title','Tests & Questions')
@section('page-subtitle','Create tests and manage MCQ question banks')
@section('topbar-actions')
    <button class="btn btn-primary topbar-btn" onclick="openModal('addTestModal')">＋ Create Test</button>
@endsection

@section('content')
<style>
.tests-grid {
    display: grid;
    grid-template-columns: 340px 1fr;
    gap: 1.25rem;
}
@media (max-width: 850px) {
    .tests-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="tests-grid">
    <!-- Tests List -->
    <div class="card" style="height:fit-content">
        <div class="card-header">
            <span class="card-title">🧪 Tests</span>
            <span id="testCountBadge" class="badge badge-purple">0</span>
        </div>
        <div id="testList" style="padding:0.5rem;min-height:200px">
            <div style="text-align:center;padding:2rem;color:var(--text-muted)"><div class="spinner-sm"></div></div>
        </div>
    </div>

    <!-- Questions Panel -->
    <div class="card">
        <div class="card-header">
            <span class="card-title" id="questionPanelTitle">📝 Questions</span>
            <div id="questionActions" style="display:none;gap:0.5rem;display:none">
                <button class="btn btn-outline btn-sm" onclick="openModal('bulkModal')">📤 Bulk Upload</button>
                <button class="btn btn-primary btn-sm" onclick="openModal('addQModal')">＋ Add Question</button>
            </div>
        </div>
        <div id="questionList">
            <div class="empty-state">
                <div class="empty-icon">🧪</div>
                <p>Select a test from the left to see its questions</p>
            </div>
        </div>
    </div>
</div>

<!-- Add Test Modal -->
<div class="modal-backdrop" id="addTestModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">➕ Create Test</span>
            <button class="modal-close" onclick="closeModal('addTestModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Chapter *</label>
                <select class="form-control" id="at-chapter"><option value="">Select chapter…</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Test Title *</label>
                <input type="text" class="form-control" id="at-title" placeholder="e.g. Bengali Chapter 1 Test">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Pass Marks *</label>
                    <input type="number" class="form-control" id="at-pass" value="10" min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">Duration (minutes) *</label>
                    <input type="number" class="form-control" id="at-duration" value="20" min="1">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addTestModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createTest()">Create Test</button>
        </div>
    </div>
</div>

<!-- Add Single Question Modal -->
<div class="modal-backdrop" id="addQModal">
    <div class="modal" style="max-width:580px">
        <div class="modal-header">
            <span class="modal-title">➕ Add Question</span>
            <button class="modal-close" onclick="closeModal('addQModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Question Text *</label>
                <textarea class="form-control" id="q-text" rows="2" placeholder="Enter the question…"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Option A *</label>
                    <input type="text" class="form-control" id="q-a" placeholder="Option A">
                </div>
                <div class="form-group">
                    <label class="form-label">Option B *</label>
                    <input type="text" class="form-control" id="q-b" placeholder="Option B">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Option C *</label>
                    <input type="text" class="form-control" id="q-c" placeholder="Option C">
                </div>
                <div class="form-group">
                    <label class="form-label">Option D *</label>
                    <input type="text" class="form-control" id="q-d" placeholder="Option D">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Correct Option *</label>
                    <select class="form-control" id="q-correct">
                        <option value="a">A</option><option value="b">B</option>
                        <option value="c">C</option><option value="d">D</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Marks</label>
                    <input type="number" class="form-control" id="q-marks" value="1" min="1">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addQModal')">Cancel</button>
            <button class="btn btn-primary" onclick="addQuestion()">Add Question</button>
        </div>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div class="modal-backdrop" id="bulkModal">
    <div class="modal" style="max-width:620px">
        <div class="modal-header">
            <span class="modal-title">📤 Bulk Question Upload (JSON)</span>
            <button class="modal-close" onclick="closeModal('bulkModal')">×</button>
        </div>
        <div class="modal-body">
            <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.75rem">Paste a JSON array of questions:</p>
            <textarea class="form-control" id="bulk-json" rows="10" style="font-family:monospace;font-size:0.8rem"
                placeholder='[{"question_text":"What is 2+2?","option_a":"3","option_b":"4","option_c":"5","option_d":"6","correct_option":"b","marks":1}]'></textarea>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('bulkModal')">Cancel</button>
            <button class="btn btn-primary" onclick="bulkUpload()">Upload Questions</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let selectedTestId = null;

async function loadTests() {
    const res = await api('GET', '/admin/tests');
    const tests = res.data?.data || [];
    document.getElementById('testCountBadge').textContent = tests.length;

    // Load chapter dropdown for add test modal
    const subRes = await api('GET', '/admin/subjects');
    let chapterOpts = '';
    for (const s of (subRes.data||[])) {
        const cr = await api('GET', `/admin/subjects/${s.id}/chapters`);
        (cr.data||[]).forEach(c => { chapterOpts += `<option value="${c.id}">[${s.name}] ${c.title}</option>`; });
    }
    document.getElementById('at-chapter').innerHTML = '<option value="">Select chapter…</option>' + chapterOpts;

    document.getElementById('testList').innerHTML = tests.length ? tests.map(t => `
        <div onclick="selectTest(${t.id},'${t.title.replace(/'/g,'\\\'')}')" id="titem-${t.id}"
            style="padding:0.875rem 1rem;border-radius:10px;cursor:pointer;transition:background 0.2s;margin:0.25rem 0;border:1px solid transparent">
            <div style="font-size:0.875rem;font-weight:600;margin-bottom:0.35rem">${t.title}</div>
            <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
                <span class="badge badge-neutral">${t.questions_count||0} Q</span>
                <span class="badge badge-neutral">${t.total_marks||0} marks</span>
                <span class="badge badge-neutral">${t.duration_minutes}min</span>
            </div>
        </div>
    `).join('') : '<div style="text-align:center;padding:2rem;color:var(--text-muted)">No tests yet. Create one!</div>';
}

function selectTest(id, title) {
    selectedTestId = id;
    document.querySelectorAll('[id^="titem-"]').forEach(el => { el.style.background=''; el.style.borderColor='transparent'; });
    const el = document.getElementById('titem-' + id);
    el.style.background = 'rgba(124,92,252,0.1)';
    el.style.borderColor = 'rgba(124,92,252,0.25)';
    document.getElementById('questionPanelTitle').textContent = `📝 ${title} — Questions`;
    const qa = document.getElementById('questionActions');
    qa.style.display = 'flex';
    loadQuestions(id);
}

async function loadQuestions(testId) {
    document.getElementById('questionList').innerHTML = '<div style="text-align:center;padding:2rem"><div class="spinner-sm"></div></div>';
    const res = await api('GET', `/admin/tests/${testId}/questions`).catch(() => null);
    // Fallback: fetch all tests and find this one
    const listRes = await api('GET', `/admin/tests?page=1`);
    const test = (listRes.data?.data||[]).find(t => t.id === testId);

    // We don't have a direct questions list endpoint in admin; questions come via bulk store
    // Show a placeholder with info
    document.getElementById('questionList').innerHTML = `
        <div style="padding:1.5rem">
            <div style="background:rgba(124,92,252,0.08);border:1px solid rgba(124,92,252,0.2);border-radius:12px;padding:1.25rem;margin-bottom:1rem">
                <div style="font-size:0.875rem;color:var(--text-secondary)">
                    📊 <strong>${test?.total_marks||0}</strong> total marks · 
                    <strong>${test?.duration_minutes||0}</strong> minutes · 
                    Pass at <strong>${test?.pass_marks||0}</strong>
                </div>
            </div>
            <div style="display:flex;gap:0.75rem;margin-bottom:1.25rem">
                <button class="btn btn-primary" onclick="openModal('addQModal')">＋ Add Single Question</button>
                <button class="btn btn-outline" onclick="openModal('bulkModal')">📤 Bulk Upload JSON</button>
            </div>
            <div style="font-size:0.8rem;color:var(--text-muted);padding:1rem;background:rgba(255,255,255,0.02);border-radius:10px;line-height:1.7">
                ℹ️ Questions are stored securely. <strong>Correct answers are never exposed to students</strong> during tests — 
                scoring happens server-side only.
            </div>
        </div>`;
}

async function createTest() {
    try {
        const passMarks = parseInt(document.getElementById('at-pass').value);
        await api('POST', '/admin/tests', {
            chapter_id: parseInt(document.getElementById('at-chapter').value),
            title: document.getElementById('at-title').value,
            total_marks: passMarks * 2, // default, will recalculate when questions added
            pass_marks: passMarks,
            duration_minutes: parseInt(document.getElementById('at-duration').value),
        });
        toast('Test created! Now add questions.', 'success');
        closeModal('addTestModal');
        loadTests();
    } catch(e) { toast(e.message, 'error'); }
}

async function addQuestion() {
    if (!selectedTestId) { toast('Please select a test first.', 'error'); return; }
    try {
        await api('POST', `/admin/tests/${selectedTestId}/questions`, { questions: [{
            question_text: document.getElementById('q-text').value,
            option_a: document.getElementById('q-a').value,
            option_b: document.getElementById('q-b').value,
            option_c: document.getElementById('q-c').value,
            option_d: document.getElementById('q-d').value,
            correct_option: document.getElementById('q-correct').value,
            marks: parseInt(document.getElementById('q-marks').value),
        }]});
        toast('Question added!', 'success');
        closeModal('addQModal');
        loadQuestions(selectedTestId);
    } catch(e) { toast(e.message, 'error'); }
}

async function bulkUpload() {
    if (!selectedTestId) { toast('Please select a test first.', 'error'); return; }
    try {
        const questions = JSON.parse(document.getElementById('bulk-json').value);
        await api('POST', `/admin/tests/${selectedTestId}/questions`, { questions });
        toast(`${questions.length} questions uploaded!`, 'success');
        closeModal('bulkModal');
        loadTests();
        loadQuestions(selectedTestId);
    } catch(e) { toast('Invalid JSON or API error: ' + e.message, 'error'); }
}

window._pageInit = () => loadTests();
</script>
@endsection
