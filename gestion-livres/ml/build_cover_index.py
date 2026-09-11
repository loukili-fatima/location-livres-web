import os
import csv
from PIL import Image
import imagehash

covers_dir = "../storage/app/public/covers"
output_csv = "cover_index.csv"

results = []

for filename in os.listdir(covers_dir):
    if not filename.lower().endswith((".jpg", ".jpeg", ".png")):
        continue

    book_id = filename.replace("book_", "").split(".")[0]
    filepath = os.path.join(covers_dir, filename)

    try:
        img = Image.open(filepath)
        phash = imagehash.phash(img)
        results.append({
            "book_id": book_id,
            "hash": str(phash)
        })
    except Exception as e:
        print(f"Erreur pour {filename} : {e}")

with open(output_csv, "w", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=["book_id", "hash"])
    writer.writeheader()
    writer.writerows(results)

print(f"Termine : {len(results)} couvertures indexees dans {output_csv}")