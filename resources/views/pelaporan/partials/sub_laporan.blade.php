@if ($type == 'select')
    <label class="form-label">Nama Sub Laporan</label>
    <select name="sub_laporan" id="sub_laporan" class="form-select select2">
        @foreach ($sub_laporan as $item)
            <option value="{{ $item['value'] }}">{{ $item['title'] }}</option>
        @endforeach
    </select>
@endif
