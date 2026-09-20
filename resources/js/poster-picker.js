// One list of poster images: drop or pick several, choose which is the main one, remove any.
// Works with ManagesPosters on the server: posters[] (new files), main_poster ("e{id}" | "n{index}")
// and remove_posters[] (ids of existing posters to delete).
export function posterPicker(existing) {
    const items = existing.map((e) => ({ id: e.id, url: e.url, existing: true }));

    return {
        items,
        files: [],
        removed: [],
        mainItem: items.find((_, i) => existing[i].main) ?? items[0] ?? null,
        dragging: false,

        keyOf(item) {
            return item.existing ? `e${item.id}` : `n${this.items.filter((i) => !i.existing).indexOf(item)}`;
        },
        get mainKey() { return this.mainItem ? this.keyOf(this.mainItem) : ''; },

        add(list) {
            [...list].filter((f) => f.type.startsWith('image/')).forEach((file) => {
                this.files.push(file);
                this.items.push({ file, url: URL.createObjectURL(file), existing: false });
            });
            if (!this.mainItem && this.items.length) this.mainItem = this.items[0];
            this.sync();
        },

        remove(item) {
            this.items = this.items.filter((i) => i !== item);
            if (item.existing) this.removed.push(item.id);
            else this.files = this.files.filter((f) => f !== item.file);
            if (this.mainItem === item) this.mainItem = this.items[0] ?? null;
            this.sync();
        },

        pick(event) {
            this.add(event.target.files);
        },

        // Keep the real <input> in step so the form submits exactly the files still listed.
        sync() {
            const dt = new DataTransfer();
            this.files.forEach((f) => dt.items.add(f));
            this.$refs.input.files = dt.files;
        },
    };
}
