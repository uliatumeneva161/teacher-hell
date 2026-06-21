let currentTestId = null;
let questions = [];
let currentQuestionIndex = 0;
let userAnswers = {};

async function api(url) {
    try {
        const response = await fetch(url);
        if (!response.ok) return null;
        return await response.json();
    } catch (error) {
        console.error('Ошибка API:', error);
        return null;
    }
}

function setText(elementId, text) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = text;
    } else {
        console.log('Элемент не найден:', elementId);
    }
}

function showNotification(message, type = 'success') {
    alert(message); 
}

async function checkAuth() {
    try {
        const response = await fetch('api/get_teacher.php');
        const data = await response.json();
        
        console.log('checkAuth ответ:', data);
        
        if (!data || !data.logged) {
            window.location.href = 'teach-login.php';
            return false;
        }
        
        return data;
    } catch (error) {
        console.error('Ошибка проверки авторизации:', error);
        window.location.href = 'teach-login.php';
        return false;
    }
}

async function loadTeacherData() {
    try {
        const response = await fetch('api/get_teacher.php');
        const data = await response.json();
        
        console.log('Данные учителя:', data);
        
        if (data && data.logged) {
            setText('teacher-name', data.name || 'Учитель');
            setText('teacher-subject', data.subject || 'Предмет');
            setText('welcome-name', data.name || '');
            setText('dashboard-subject', data.subject || '');
            setText('tests-subject', data.subject || '');
            setText('profile-name', data.name || '');
            setText('profile-subject', data.subject || '');
            setText('profile-login', data.login || '');
        }
    } catch (error) {
        console.error('Ошибка загрузки данных учителя:', error);
    }
}

async function loadDashboard() {
    const stats = await api('api/get_teacher_stats.php');
    if (stats) {
        setText('tests-count', stats.available || 0);
        setText('completed-tests', stats.completed || 0);
        setText('average-score', (stats.average || 0) + '%');
    }
    
    const recent = await api('api/get_recent_results.php');
    const container = document.getElementById('recent-tests-list');
    
    if (container) {
        if (!recent || recent.length === 0) {
            container.innerHTML = '<tr><td colspan="3">Нет результатов</td></tr>';
        } else {
            let html = '';
            recent.forEach(r => {
                html += `<tr>
                    <td>${r.test_name || 'Тест'}</td>
                    <td>${r.date || '—'}</td>
                    <td>${r.score || 0}%</td>
                </tr>`;
            });
            container.innerHTML = html;
        }
    }
}

async function loadTests() {
    console.log('Загрузка тестов...');
    
    const tests = await api('api/get_teacher_tests.php');
    const availableGrid = document.getElementById('tests-available-grid');
    const completedGrid = document.getElementById('tests-completed-grid');
    const allGrid = document.getElementById('tests-all-grid');
    
    if (!availableGrid || !completedGrid || !allGrid) return;
    
    if (!tests || tests.length === 0) {
        availableGrid.innerHTML = '<p>Нет доступных тестов</p>';
        completedGrid.innerHTML = '<p>Нет пройденных тестов</p>';
        allGrid.innerHTML = '<p>Нет тестов</p>';
        return;
    }
    
    availableGrid.innerHTML = '';
    completedGrid.innerHTML = '';
    allGrid.innerHTML = '';
    
    tests.forEach(test => {
        const card = document.createElement('div');
        card.className = 'test-card';
        card.innerHTML = `
            <h3>${test.name}</h3>
            <p>Вопросов: ${test.question_count}</p>
            <button class="btn-start-test" data-id="${test.id}">
                ${test.completed ? 'Пройти заново' : 'Начать тест'}
            </button>
        `;
        
        allGrid.appendChild(card.cloneNode(true));
        
        if (!test.completed) {
            availableGrid.appendChild(card.cloneNode(true));
        }
    
        if (test.completed) {
            completedGrid.appendChild(card.cloneNode(true));
        }
    });
}
function initTabs() {
    const tabs = document.querySelectorAll('.category-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const category = this.dataset.category;
            document.querySelectorAll('.category-content').forEach(c => {
                c.classList.remove('active');
            });
            
            const target = document.getElementById('category-' + category);
            if (target) target.classList.add('active');
        });
    });
}

async function startTest(testId) {
    console.log('Начало теста:', testId);
    
    try {
        const response = await fetch(`api/get_test_questions.php?test_id=${testId}`);
        questions = await response.json();
        
        if (!questions || questions.length === 0) {
            alert('Тест не содержит вопросов');
            return;
        }
        
        currentTestId = testId;
        currentQuestionIndex = 0;
        userAnswers = {};
        
        setText('test-questions-count', questions.length);
        document.getElementById('test-modal').classList.add('active');
        renderQuestion();
    } catch (error) {
        console.error('Ошибка загрузки теста:', error);
        alert('Ошибка загрузки теста');
    }
}

function renderQuestion() {
    if (!questions.length) return;
    
    const q = questions[currentQuestionIndex];
    const container = document.getElementById('question-container');
    
    if (!container) return;
    
    let html = `<h4>Вопрос ${currentQuestionIndex + 1} из ${questions.length}</h4>`;
    html += `<p>${q.question_text}</p>`;
    
    q.options.forEach(opt => {
        const isChecked = userAnswers[q.id] && userAnswers[q.id].includes(opt.id);
        const checkedAttr = isChecked ? 'checked' : '';
        
        html += `
            <div class="option-item">
                <label>
                    <input type="checkbox" name="answer" value="${opt.id}" ${checkedAttr}>
                    ${opt.text}
                </label>
            </div>
        `;
    });
    
    container.innerHTML = html;
    setText('question-counter', `${currentQuestionIndex + 1} / ${questions.length}`);
    
    const prevBtn = document.getElementById('prev-question');
    const nextBtn = document.getElementById('next-question');
    const finishBtn = document.getElementById('finish-test');
    
    if (prevBtn) prevBtn.disabled = currentQuestionIndex === 0;
    
    if (currentQuestionIndex === questions.length - 1) {
        if (nextBtn) nextBtn.style.display = 'none';
        if (finishBtn) finishBtn.style.display = 'inline-block';
    } else {
        if (nextBtn) nextBtn.style.display = 'inline-block';
        if (finishBtn) finishBtn.style.display = 'none';
    }
}

function nextQuestion() {
    saveAnswer();
    if (currentQuestionIndex < questions.length - 1) {
        currentQuestionIndex++;
        renderQuestion();
    }
}

function prevQuestion() {
    saveAnswer();
    if (currentQuestionIndex > 0) {
        currentQuestionIndex--;
        renderQuestion();
    }
}

function saveAnswer() {
    const questionId = questions[currentQuestionIndex].id;
    const selectedCheckboxes = document.querySelectorAll('input[name="answer"]:checked');
    
    if (selectedCheckboxes.length > 0) {
        userAnswers[questionId] = Array.from(selectedCheckboxes).map(cb => parseInt(cb.value));
    } else {
        userAnswers[questionId] = [];
    }
}

async function finishTest() {
    saveAnswer();
    
    let correct = 0;
    const total = questions.length;
    
    for (let q of questions) {
        let selectedIds = userAnswers[q.id] || [];
        let correctOptionIds = q.options
            .filter(opt => opt.isCorrect == 1 || opt.isCorrect === true)
            .map(opt => Number(opt.id));
        
        selectedIds = selectedIds.map(id => Number(id));
        selectedIds.sort((a, b) => a - b);
        correctOptionIds.sort((a, b) => a - b);
        
        const isCorrect = selectedIds.length === correctOptionIds.length &&
                          selectedIds.every((id, index) => id === correctOptionIds[index]);
        
        if (isCorrect) {
            correct++;
        }
    }
    
    try {
        const response = await fetch('api/save_result.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                test_id: currentTestId,
                correct: correct,
                total: total
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            const percent = Math.round(correct / total * 100);
            alert(`Тест завершён! Правильных ответов: ${correct} из ${total} (${percent}%)`);
        } else {
            alert('Ошибка: ' + (result.error || 'Неизвестная ошибка'));
        }
    } catch (error) {
        console.error('Ошибка сохранения результата:', error);
        alert('Ошибка сохранения результата');
    }
    
    document.getElementById('test-modal').classList.remove('active');
    loadTests();
}

async function loadResults() {
    console.log('Загрузка результатов...');
    
    const results = await api('api/get_teacher_results.php');
    const tbody = document.getElementById('results-table-body');
    
    if (!tbody) return;
    
    if (!results || results.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6">Нет результатов</td></tr>';
        return;
    }
    
    let html = '';
    results.forEach(r => {
        html += `<tr>
            <td>${r.test_name}</td>
            <td>${r.subject_name || '—'}</td>
            <td>${r.completed_at || '—'}</td>
            <td>${r.correct_answers}/${r.total_questions}</td>
            <td>${r.score}%</td>
            <td>${r.score >= 80 ? 'Отлично' : r.score >= 60 ? 'Хорошо' : r.score >= 60 ? 'Удовл-но' : 'Неудовл-но'}</td>
        </tr>`;
    });
    tbody.innerHTML = html;
}

async function loadProfile() {
    console.log('Загрузка профиля...');
    
    const profile = await api('api/get_teacher_profile.php');
    if (!profile) return;
    
    setText('profile-name', profile.name || '');
    setText('profile-login', profile.login || '');
    setText('profile-subject', profile.subject || 'Не указан');
    setText('profile-date', profile.registered || '—');
    
    if (profile.stats) {
        setText('total-tests', profile.stats.total_tests || 0);
        setText('passed-tests', profile.stats.completed_tests || 0);
        setText('best-result', (profile.stats.best_score || 0) + '%');
        setText('avg-time', (profile.stats.average_score || 0) + '%');
    }
}

async function logoutTeacher() {
    await fetch('api/logout_teacher.php');
    window.location.href = '../index.php';
}

document.addEventListener('DOMContentLoaded', async function() {
    console.log('Страница загружена, проверяем авторизацию...');
    const teacherData = await checkAuth();
    if (!teacherData) return;
    await loadTeacherData();
    const path = window.location.pathname;
    const page = path.split('/').pop();
    
    if (page === 'index.html' || page === '') {
        await loadDashboard();
    } else if (page === 'currTest.html') {
        await loadTests();
        initTabs();
    } else if (page === 'results.html') {
        await loadResults();
    } else if (page === 'profile.html') {
        await loadProfile();
    }

    const modal = document.getElementById('test-modal');
    if (modal) {
        const closeBtn = document.getElementById('close-test-modal');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.classList.remove('active');
            });
        }
        
        const prevBtn = document.getElementById('prev-question');
        if (prevBtn) prevBtn.addEventListener('click', prevQuestion);
        
        const nextBtn = document.getElementById('next-question');
        if (nextBtn) nextBtn.addEventListener('click', nextQuestion);
        
        const finishBtn = document.getElementById('finish-test');
        if (finishBtn) finishBtn.addEventListener('click', finishTest);
    }
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-start-test')) {
            const testId = e.target.dataset.id;
            startTest(testId);
        }
    });
    
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            logoutTeacher();
        });
    }
});