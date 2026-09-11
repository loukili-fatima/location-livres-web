import sys
import csv
import json
from PIL import Image
import imagehash

def main():
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Aucune image fournie"}))
        return

    image_path = sys.argv[1]

    try:
        img = Image.open(image_path)
        query_hash = imagehash.phash(img)
    except Exception as e:
        print(json.dumps({"error": f"Impossible de lire l'image : {e}"}))
        return

    results = []

    with open("cover_index.csv", "r") as f:
        reader = csv.DictReader(f)
        for row in reader:
            indexed_hash = imagehash.hex_to_hash(row["hash"])
            distance = int(query_hash - indexed_hash)
            results.append({
                "book_id": int(row["book_id"]),
                "distance": distance
            })

    results.sort(key=lambda x: x["distance"])

    top_matches = results[:5]

    print(json.dumps({"matches": top_matches}))

if __name__ == "__main__":
    main()