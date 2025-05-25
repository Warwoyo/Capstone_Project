
@props(['student' => [], 'scheduleDetail' => [], 'existingScore' => [], 'readonly' => false])

<div class="p-4 bg-white border border-sky-300 rounded-lg">
    <div class="flex justify-between items-start mb-3">
        <div>
            <h4 class="font-medium text-sky-800" x-text="student.name"></h4>
            <p class="text-sm text-gray-600" x-text="student.student_number"></p>
        </div>
        <div class="text-right">
            <div class="text-sm text-gray-600">Nilai:</div>
            <select x-model="scores[student.id].score" 
                    class="mt-1 px-2 py-1 border border-sky-300 rounded text-sm"
                    :disabled="readonly">
                <option value="">-- Pilih --</option>
                <option value="1">1 - Belum Berkembang</option>
                <option value="2">2 - Mulai Berkembang</option>
                <option value="3">3 - Berkembang Sesuai Harapan</option>
                <option value="4">4 - Berkembang Sangat Baik</option>
            </select>
        </div>
    </div>
    <div>
        <label class="block text-sm text-gray-600 mb-1">Catatan:</label>
        <textarea x-model="scores[student.id].note" 
                  class="w-full px-3 py-2 border border-sky-300 rounded text-sm resize-none"
                  rows="2" 
                  placeholder="Tambahkan catatan observasi..."
                  :disabled="readonly"></textarea>
    </div>
</div>