<div class="card">

    <div class="card-body space-y-5">

        <div>

            <label class="form-label">

                Nama Metode

            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name',$paymentMethod->name ?? '') }}"
                class="form-input">

        </div>

        <div>

            <label class="form-label">

                Jenis

            </label>

            <select
                name="type"
                class="form-select">

                <option value="bank"
                    @selected(old('type',$paymentMethod->type ?? '')=='bank')>

                    Transfer Bank

                </option>

                <option value="qris"
                    @selected(old('type',$paymentMethod->type ?? '')=='qris')>

                    QRIS

                </option>

                <option value="ewallet"
                    @selected(old('type',$paymentMethod->type ?? '')=='ewallet')>

                    E-Wallet

                </option>

            </select>

        </div>

        <div>

            <label class="form-label">

                Nomor Rekening / Nomor Akun

            </label>

            <input
                type="text"
                name="account_number"
                value="{{ old('account_number',$paymentMethod->account_number ?? '') }}"
                class="form-input">

        </div>

        <div>

            <label class="form-label">

                Atas Nama

            </label>

            <input
                type="text"
                name="account_name"
                value="{{ old('account_name',$paymentMethod->account_name ?? '') }}"
                class="form-input">

        </div>

        <div>

            <label class="form-label">

                QR Code

            </label>

            @isset($paymentMethod)

                @if($paymentMethod->image)

                    <img
                        src="{{ asset('storage/'.$paymentMethod->image) }}"
                        class="w-40 rounded border mb-3">

                @endif

            @endisset

            <input
                type="file"
                name="image"
                class="form-input">

        </div>

        <div>

            <label class="form-label">

                Catatan

            </label>

            <textarea
                rows="4"
                name="notes"
                class="form-textarea">{{ old('notes',$paymentMethod->notes ?? '') }}</textarea>

        </div>

        <div>

            <label class="form-label">

                Urutan Tampil

            </label>

            <input
                type="number"
                name="sort_order"
                value="{{ old('sort_order',$paymentMethod->sort_order ?? 0) }}"
                class="form-input">

        </div>

        <label class="flex items-center gap-2">

            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old('is_active',$paymentMethod->is_active ?? true))
                class="rounded">

            <span>

                Aktifkan metode pembayaran

            </span>

        </label>

    </div>

    <div class="card-footer flex justify-end gap-3">

        <a
            href="{{ route('admin.payment-methods.index') }}"
            class="btn btn-secondary">

            Batal

        </a>

        <button
            class="btn btn-primary">

            Simpan

        </button>

    </div>

</div>