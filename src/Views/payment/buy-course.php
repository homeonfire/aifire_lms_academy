<?php include 'src/Views/layouts/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                <h1 class="text-2xl font-bold text-white">Покупка курса</h1>
            </div>
            
            <div class="p-6">
                <div class="flex items-center space-x-4 mb-6">
                    <?php if ($course['cover_url']): ?>
                        <img src="<?= htmlspecialchars($course['cover_url']) ?>" 
                             alt="<?= htmlspecialchars($course['title']) ?>" 
                             class="w-24 h-24 object-cover rounded-lg">
                    <?php else: ?>
                        <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($course['title']) ?></h2>
                        <p class="text-gray-600"><?= htmlspecialchars($course['difficulty_level']) ?></p>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars($course['description'])) ?></p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-medium text-gray-900">Стоимость курса:</span>
                        <span class="text-2xl font-bold text-green-600">
                            <?php if ($course['is_free']): ?>
                                Бесплатно
                            <?php else: ?>
                                <?= number_format($course['price'], 0, ',', ' ') ?> ₽
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <?php if ($course['is_free']): ?>
                    <form action="/payment/create-payment" method="POST" class="space-y-4">
                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                        <button type="submit" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                            Получить курс бесплатно
                        </button>
                    </form>
                <?php else: ?>
                    <form action="/payment/create-payment" method="POST" class="space-y-4">
                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-blue-800 text-sm">
                                    Оплата производится через Т-Банк. Ваши данные защищены.
                                </span>
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Оплатить <?= number_format($course['price'], 0, ',', ' ') ?> ₽
                        </button>
                    </form>
                <?php endif; ?>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="/courses/show?id=<?= $course['id'] ?>" 
                       class="text-blue-600 hover:text-blue-800 text-sm">
                        ← Вернуться к курсу
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'src/Views/layouts/footer.php'; ?> 