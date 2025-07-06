<?php include 'src/Views/layouts/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h1 class="text-2xl font-bold text-white">Оплата прошла успешно!</h1>
                </div>
            </div>
            
            <div class="p-6">
                <?php if ($payment): ?>
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">
                            Курс "<?= htmlspecialchars($payment['course_title']) ?>" оплачен
                        </h2>
                        <p class="text-gray-600">
                            Спасибо за покупку! Теперь вы можете приступить к изучению курса.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Номер платежа:</span>
                                <p class="font-medium">#<?= $payment['id'] ?></p>
                            </div>
                            <div>
                                <span class="text-gray-500">Сумма:</span>
                                <p class="font-medium"><?= number_format($payment['amount'], 0, ',', ' ') ?> ₽</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Дата:</span>
                                <p class="font-medium"><?= date('d.m.Y H:i', strtotime($payment['created_at'])) ?></p>
                            </div>
                            <div>
                                <span class="text-gray-500">Статус:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Оплачен
                                </span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">
                            Оплата прошла успешно!
                        </h2>
                        <p class="text-gray-600">
                            Спасибо за покупку! Теперь вы можете приступить к изучению курса.
                        </p>
                    </div>
                <?php endif; ?>

                <div class="space-y-3">
                    <a href="/courses" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Перейти к курсам
                    </a>
                    
                    <a href="/dashboard" 
                       class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                        </svg>
                        Личный кабинет
                    </a>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Что дальше?</p>
                                <p>Курс добавлен в ваш личный кабинет. Вы можете начать обучение в любое время. Если у вас возникнут вопросы, обратитесь в службу поддержки.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'src/Views/layouts/footer.php'; ?> 